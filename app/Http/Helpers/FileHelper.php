<?php


namespace App\Http\Helpers;


class FileHelper
{
    /**
     * @param $data
     * @return array
     */
    public static function propsToArray($data){
        $array = [];
        foreach ($data as $row){
            $array = array_unique(array_merge($array,array_keys($row)));
        }
        return array_values($array);
    }

    public static function getCsvHeaders(){
        $fileName = 'fu3e.csv';
        return array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
    }
    public static function exportCsvFile($data, $columns){
        return function () use ($data, $columns){
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
    }
}
