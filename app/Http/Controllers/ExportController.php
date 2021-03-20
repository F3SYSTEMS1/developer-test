<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GenerateCsvRequest;
use App\Services\CsvService;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Converts the user input into a CSV file and streams the file back to the user
     *
     * @param GenerateCsvRequest $request
     * @param CsvService $csvService
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function convertToCsv(GenerateCsvRequest $request, CsvService $csvService)
    {
        try {
            $result = $csvService->setPrintHeader((bool)$request->get('include_headers'))
                ->generateCsv($request->get('data'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('errors.export_failed'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $result;
    }
}
