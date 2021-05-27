<?php

namespace Tests\Unit;

use App\Services\CsvService;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    const TESTING_DATA = [
        ['firstName' => 'John', 'lastName' => 'Doe']
    ];

    public function testExportToCsvWithInvalidResult()
    {
        $this->patchJson(
            '/api/csv-export',
            [
                'data' => []
            ]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['data']);
    }

    public function testExportToCsvWithSuccessResult()
    {
        $response = $this->patchJson(
            '/api/csv-export',
            [
                'data' => self::TESTING_DATA
            ]
        );

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $response = new TestResponse(
            new Response(
                $content,
                $response->baseResponse->getStatusCode(),
                $response->baseResponse->headers->all()
            )
        );

        $response->assertStatus(200)
            ->assertSeeText('John')
            ->assertSeeText('Doe');
    }

    public function testExportToCsvWithException()
    {
        $this->mock(
            CsvService::class,
            function ($mock) {
                $mock->shouldReceive('export')
                    ->andThrow(new \Exception('Server error'));
            }
        );

        $this->patchJson(
            '/api/csv-export',
            [
                'data' => self::TESTING_DATA
            ]
        )
            ->assertStatus(500)
            ->assertJson(
                [
                    'message' => 'CSV export is failed'
                ]
            );
    }
}
