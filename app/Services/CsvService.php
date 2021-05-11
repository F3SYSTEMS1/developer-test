<?php

declare(strict_types=1);
namespace App\Services;


class CsvService
{
    /**
     * @return string[]
     */
    public function getHeaderConfig(): array
    {
        return [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=data.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
    }
    /**
     * @param array $data
     * @return string[]
     */
    public function makeHeaderTitles(array $data): array
    {
        return array_keys($data['data'][0] ?? []);
    }

    /**
     * @param array $data
     * @return array
     */
    public function makeData(array $data): array
    {
        return $data['data'] ?? [];
    }

}
