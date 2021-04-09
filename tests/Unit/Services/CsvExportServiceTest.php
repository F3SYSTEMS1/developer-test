<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\CsvExportService;
use Mockery;
use PHPUnit\Framework\TestCase;

class CsvExportServiceTest extends TestCase
{

    /**
     * @var Mockery\MockInterface
     */
    private $service;

    /**
     * @throws CsvExportService
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new CsvExportService();
    }

    /**
     * @return void
     */
    public function testGetHeaderConfig(): void
    {
        $this->assertIsArray($this->service->getHeaderConfig());
    }

    /**
     * @dataProvider prepareHeaderTitles
     * @param array $data
     * @param array $expect
     *
     * @return void
     */
    public function testPrepareHeaderTitles(array $data, array $expect): void
    {
        $this->assertEquals($expect, $this->service->prepareHeaderTitles($data));
    }

    public function prepareHeaderTitles(): array
    {
        return [
            [[], []],
            [['data' => [['first_name' => 'Alex']]], ['first_name']],
            [['data' => [['first_name' => 'Alex', 'last_name' => 'Stet']]], ['first_name', 'last_name']],
        ];
    }

    /**
     * @dataProvider prepareData
     * @param array $data
     * @param array $expect
     *
     * @return void
     */
    public function testPrepareData(array $data, array $expect): void
    {
        $this->assertEquals($expect, $this->service->prepareData($data));
    }

    public function prepareData(): array
    {
        return [
            [[], []],
            [['data' => [['first_name' => 'Alex']]], [['first_name' => 'Alex']]],
        ];
    }
}
