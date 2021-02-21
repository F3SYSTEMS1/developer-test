<?php

namespace Tests\ControllersTests;

use Tests\TestCase;

class CsvExportTest extends TestCase
{
    /**
     * Check possibility of CsvExport controller parse info from frontend
     * and create CSV file with proper response.
     *
     * @test
     * @return void
     */
    public function controllerCanCreateCsvFile()
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

    /**
     * Check possibility of CsvExport controller generate proper response
     * if request data is empty or unparsable.
     *
     * @test
     * @return void
     */
    public function controllerCanNotCreateCsvFile()
    {
        $task_arr = [
            //
        ];

        $response = $this->json('PATCH', 'api/csv-export', $task_arr);
        $response->assertStatus(400);
    }
}
