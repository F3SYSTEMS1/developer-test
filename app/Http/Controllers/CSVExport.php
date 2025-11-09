<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CSVExport
{
    public function __invoke(Request $request): Response
    {
        $rows = $request->json()->all();

        if (!is_array($rows) || empty($rows) || !is_array($rows[0])) {
            abort(400, 'Expected a JSON array.');
        }

        $headers = array_keys($rows[0]);

        $csv = implode(',', $headers) . "\r\n";

        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $key) {
                $value = $row[$key] ?? '';
                if (preg_match('/[,"\r\n]/', $value)) {
                    $value = '"' . str_replace('"', '""', $value) . '"';
                }

                $line[] = $value;
            }

            $csv .= implode(',', $line) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'  => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
