<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CsvRequest;
use App\Services\CsvService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class CsvController
 * @package App\Http\Controllers
 */
class CsvController extends Controller
{
    /**
     * @var CsvService
     */
    private $csvService;

    /**
     * CsvExport constructor.
     * @param CsvService $csvService
     */
    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    /**
     * @param CsvRequest $request
     * @return \Illuminate\Http\JsonResponse|StreamedResponse
     */
    public function export(CsvRequest $request)
    {
        try {
            $callback = $this->csvService->export($request->get('data'));
            $response = response()->stream(
                $callback,
                Response::HTTP_OK,
                $this->csvService->getHeaders()
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'CSV export is failed',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }
}
