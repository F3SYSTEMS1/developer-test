<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    private const URI = '/api/csv-export';

    /**
     *
     * @dataProvider getMockData
     *
     * @param array $data
     * @param string $expected
     */
    public function testCsvExport(array $data, string $expected): void
    {
        ob_start();
        $response = $this->json(Request::METHOD_PATCH, self::URI, $data)->send();
        $content = ob_get_clean();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals($expected, $content);
    }

    public function getMockData(): iterable
    {
        $expected = <<<'TEXT'
"first name","last name"
John,Doe
Arya,Stark

TEXT;

        yield [
            [
                [
                    'first name' => 'John',
                    'last name' => 'Doe',
                ],
                [
                    'first name' => 'Arya',
                    'last name' => 'Stark',
                ],
            ],
            $expected,
        ];
    }
}
