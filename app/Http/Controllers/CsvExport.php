<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CsvExportService;
use Illuminate\Http\Request;

class CsvExport extends Controller
{
    /**
     * Converts the user input into a CSV file and streams the file back to the user
     *
     * @param Request $request
     * @param CsvExportService $csvExportService
     */
    public function convert(Request $request, CsvExportService $csvExportService)
    {
        try {
            $data = json_decode($request->getContent(), true);
        } catch (\Throwable $exception) {
            return ['success' => false];
        }

        return response()->stream($csvExportService->getStream($data));
    }
}
