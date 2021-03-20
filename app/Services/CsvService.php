<?php

declare(strict_types=1);

namespace App\Services;

class CsvService
{
    private string $delimiter = ',';
    private string $enclosure = '"';
    private string $escapeChar = '\\';
    private bool $printHeader = true;

    public function generateCsv($data): string
    {
        $file = fopen('php://memory', 'r+');
        $columns = [];

        if ($this->printHeader) {
            $columns = array_keys($data[0]);

            fputcsv($file, $columns, $this->delimiter, $this->enclosure, $this->escapeChar);
        }

        if (count($columns)) {
            $data = array_map(function ($item) use ($columns) {
                $result = [];
                foreach ($columns as $column) {
                    $result[$column] = $item[$column] ?? null;
                }
                return $result;
            }, $data);
        }

        foreach ($data as $item) {
            fputcsv($file, $item, $this->delimiter, $this->enclosure, $this->escapeChar);
        }

        rewind($file);

        return stream_get_contents($file);
    }

    public function setPrintHeader(bool $shouldPrint): self
    {
        $this->printHeader = $shouldPrint;

        return $this;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }
}
