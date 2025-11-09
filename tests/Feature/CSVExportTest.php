<?php

namespace Tests\Feature;

use Tests\TestCase;

class CSVExportTest extends TestCase
{
    // Test that the correct data returns in a csv format.
    public function test_returned_csv_data(): void
    {
        $payload = [
            ['firstName' => 'John', 'lastName' => 'Doe', 'emailAddress' => 'john@example.com'],
            ['firstName' => 'Jane', 'emailAddress' => 'jane@example.com'],
        ];

        $res = $this->patchJson('/api/csv-export', $payload);

        $res->assertOk();
        $res->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $csv = $res->getContent();

        if (str_starts_with($csv, "\xEF\xBB\xBF")) {
            $csv = substr($csv, 3);
        }

        $lines = preg_split("/\r\n|\n|\r/", trim($csv));
        $this->assertGreaterThanOrEqual(3, count($lines), 'CSV should have header + 2 data rows');

        $header = str_getcsv($lines[0]);
        $this->assertContains('firstName', $header);
        $this->assertContains('lastName', $header);
        $this->assertContains('emailAddress', $header);

        $idx = array_flip($header);

        $row1 = str_getcsv($lines[1]);
        $this->assertSame('John', $row1[$idx['firstName']]);
        $this->assertSame('Doe', $row1[$idx['lastName']]);
        $this->assertSame('john@example.com', $row1[$idx['emailAddress']]);

        $row2 = str_getcsv($lines[2]);
        $this->assertSame('Jane', $row2[$idx['firstName']]);
        $this->assertSame('', $row2[$idx['lastName']]);
        $this->assertSame('jane@example.com', $row2[$idx['emailAddress']]);
    }

    // Test that we get a status 400 response when no data is sent.
    public function test_bad_request_response_when_no_data(): void
    {
        $res = $this->patchJson('/api/csv-export', [null]);
        $res->assertStatus(400);
    }

    // Test that we get a status 400 response when invalid data is sent.
    public function test_bad_request_response_when_invalid_data(): void
    {
        $res = $this->patchJson('/api/csv-export', ['string value']);
        $res->assertStatus(400);
    }

    // Test that response data is text.
    public function test_response_content_is_text(): void
    {
        $payload = [['a' => 1]];
        $res = $this->patchJson('/api/csv-export', $payload);
        $res->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
