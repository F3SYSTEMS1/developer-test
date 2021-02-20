<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsvExport extends Controller {
    /**
     * Converts the user input into a CSV file and streams the file back to the user
     */
    public function convert(Request $request)
    {
        $list = json_decode($request->getContent(), true);
        if(empty($list)) return response()->json([], 400);

        // add columns names
        array_unshift($list, array_keys($list[0]));

        $headers = [
            'Content-Disposition' => '[{"filename" : "export.csv"}]',
        ];

        return response()->stream($this->exportCSVFile($list), 200, $headers);
    }

    protected function exportCSVFile($list = null) {
        return function() use ($list)
        {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };
    }
}
