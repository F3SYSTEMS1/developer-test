<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CsvRequest;
use App\Services\CsvService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExport extends Controller {

    /**
     * @var CsvService
     */
    private $csvService;

    /**
     * CsvExport constructor.
     * @param  CsvService  $csvExportService
     */
    public function __construct(CsvService $csvExportService)
    {
        $this->csvService = $csvExportService;
    }

    public function convert(CsvRequest $request): StreamedResponse
    {
        $csvData = $request->validated();

            $callback = function() use($csvData) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $this->csvService->makeHeaderTitles($csvData));

            foreach ($this->csvService->makeData($csvData) as $item) {
                fputcsv($file, $item);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $this->csvService->getHeaderConfig());
    }
}
