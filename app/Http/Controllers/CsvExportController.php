<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CsvExportRequest;
use App\Services\CsvExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportController extends Controller
{

    /**
     * @var CsvExportService
     */
    private $csvExportService;

    /**
     * CsvExport constructor.
     * @param  CsvExportService  $csvExportService
     */
    public function __construct(CsvExportService $csvExportService)
    {
        $this->csvExportService = $csvExportService;
    }

    /**
     * Converts the user input into a CSV file and streams the file back to the user
     * @param CsvExportRequest $request
     * @return StreamedResponse
     */
    public function convert(CsvExportRequest $request): StreamedResponse
    {
        $csvData = $request->validated();

        $callback = function() use($csvData) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $this->csvExportService->prepareHeaderTitles($csvData));

            foreach ($this->csvExportService->prepareData($csvData) as $item) {
                fputcsv($file, $item);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $this->csvExportService->getHeaderConfig());
    }
}
