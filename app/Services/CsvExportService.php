<?php

declare(strict_types=1);

namespace App\Services;

class CsvExportService implements ExportServiceInterface
{
    /**
     * @param array $data
     *
     * @return callable
     */
    public function getStream(array $data): callable
    {
        array_unshift($data, $this->getHeader($data));

        return function() use ($data) {
            $stream = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($stream, $row);
            }
            fclose($stream);
        };
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function getHeader($data): array
    {
        return (count($data) > 0) ? array_keys($data[0]) : [];
    }
}
