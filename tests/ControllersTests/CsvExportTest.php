<?php

namespace Tests\ControllersTests;

use Tests\TestCase;

class CsvExportTest extends TestCase
{
    /** @test */
    public function controller_can_create_CSV_file()
    {
        $task_arr = [
            [
                'first_name'   => 'John',
                'last_name'    => 'Doe',
                'emailAddress' => 'john.doe@example.com'
            ],
            [
                'first_name'   => 'John',
                'last_name'    => 'Doe',
                'emailAddress' => 'john.doe@example.com'
            ]
        ];

        $response = $this->json('PATCH', 'api/csv-export', $task_arr);
        $response->assertSuccessful();
        $response->assertHeader('Content-Disposition', '[{"filename" : "export.csv"}]');
    }

    /** @test */
    public function controller_can_not_create_CSV_file()
    {
        $task_arr = [
            //
        ];

        $response = $this->json('PATCH', 'api/csv-export', $task_arr);
        $response->assertStatus(400);
    }
}
