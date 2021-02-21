<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsvExport extends Controller {
    /**
     * Converts the user input into a CSV file and streams the file back to the user.
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception If request data is empty or unparsable
     */
    public function convert(Request $request)
    {
        try {
            $list = json_decode($request->getContent(), true);

            // Add columns names
            array_unshift($list, array_keys($list[0]));

        } catch (\Exception $e) {
            return response()->json([], 400);
        }

        $headers = [
            'Content-Disposition' => '[{"filename" : "export.csv"}]',
        ];

        return response()->stream($this->exportCSVFile($list), 200, $headers);
    }

    /**
     * Create CSV file, based on data from request.
     *
     * @param array $list
     * @return mixed
     */
    protected function exportCSVFile($list) {
        return function() use ($list)
        {
            $file_stream = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($file_stream, $row);
            }
            fclose($file_stream);
        };
    }
}
