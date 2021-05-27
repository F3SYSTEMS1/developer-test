<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Class CsvService
 * @package App\Services
 */
class CsvService
{
    /**
     * @param array $data
     * @return \Closure
     */
    public function export(array $data): \Closure
    {
        return function () use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, array_keys($data[0]));

            foreach ($data as $item) {
                fputcsv($file, $item);
            }

            fclose($file);
        };
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=data.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
    }
}
