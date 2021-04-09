<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class CsvExportControllerTest extends TestCase
{

    /**
     * @return void
     */
    public function testConvertActionFailsValidation(): void
    {
        $this->patchJson('/api/csv-export', [])
            ->assertStatus(422);
    }

    /**
     * @return void
     */
    public function testConvertAction(): void
    {
        $response = $this->patchJson('/api/csv-export', [
            'data' => [
                [
                    'emailAddress' => 'john.doe@example.com',
                    'first_name' => 'John',
                    'last_name' =>  'Doe'
                ],
            ]
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=data.csv');

        $this->assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
