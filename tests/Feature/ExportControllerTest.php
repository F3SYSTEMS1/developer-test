<?php

namespace Tests\Feature;

use App\Services\CsvService;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    private array $testData = [
        ['name' => 'John', 'email' => 'test@example.com']
    ];

    public function testConvertToCsvWithInvalidData()
    {
        $this->patchJson('/api/csv-export', [
            'data' => [],
            'include_headers' => true
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['data']);

        $this->patchJson('/api/csv-export', [
            'data' => $this->testData,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['include_headers']);
    }

    public function testConvertToCsvWithCorrectDataAndHeaders()
    {
        $response = $this->patchJson('/api/csv-export', [
            'data' => $this->testData,
            'include_headers' => true
        ]);

        $response->assertStatus(200)
            ->assertSeeText('name')
            ->assertSeeText('email')
            ->assertSeeText('John')
            ->assertSeeText('test@example.com');
    }

    public function testConvertToCsvWithCorrectDataWithoutHeaders()
    {
        $response = $this->patchJson('/api/csv-export', [
            'data' => $this->testData,
            'include_headers' => false
        ]);

        $response->assertStatus(200)
            ->assertDontSeeText('name')
            ->assertDontSeeText('email')
            ->assertSeeText('John')
            ->assertSeeText('test@example.com');
    }

    public function testConvertToCsvWithException()
    {
        $this->mock(CsvService::class, function ($mock) {
            $mock->shouldReceive('generateCsv')
                ->andThrow(new \Exception('Server error'));
        });

        $this->patchJson('/api/csv-export', [
            'data' => $this->testData,
            'include_headers' => false
        ])
            ->assertStatus(500)
            ->assertJson([
                'message' => 'Export failed'
            ]);
    }
}
