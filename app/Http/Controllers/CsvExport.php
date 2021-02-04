<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Helpers\FileHelper;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
class CsvExport extends Controller {
    /**
     * Converts the user input into a CSV file and streams the file back to the user
     */
    public function convert(Request $request)
    {
        $data = $request->all();

        $columns = FileHelper::propsToArray($data);

        return response()->stream(FileHelper::exportCsvFile($data, $columns), 200, FileHelper::getCsvHeaders());
    }
}
