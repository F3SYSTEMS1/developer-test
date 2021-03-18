<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\CsvExportService;
use PHPUnit\Framework\TestCase;

class CsvExportServiceTest extends TestCase
{
    /**
     * @var CsvExportService
     */
    private $exportService;

    public function setUp(): void
    {
        parent::setUp();

        //$this->exportService = $this->createMock(CsvExportService::class);
        $this->exportService = new CsvExportService();
    }

    /**
     * @dataProvider getMockData
     *
     * @param array $data
     * @param string $expected
     */
    public function testGetStream(array $data, string $expected): void
    {
        ob_start();
        $this->exportService->getStream($data)->call($this);
        $actual = ob_get_clean();
        self::assertEquals($expected, $actual);
    }

    public function getMockData(): iterable
    {
        $expected1 = <<<'TEXT'
"first name","last name"
John,Doe
Arya,Stark

TEXT;
        $expected2 = <<<'TEXT'
name,age,sex
"Doe, Jane",36,F

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
            $expected1,
        ];

        yield [
            [
                [
                    'name' => 'Doe, Jane',
                    'age' => 36,
                    'sex' => 'F',
                ],
            ],
            $expected2,
        ];
    }

    public function tearDown(): void
    {
        unset($this->exportService);

        parent::tearDown();
    }
}
