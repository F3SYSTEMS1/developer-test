<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class CsvRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'data.*' => ['required', 'array'],
        ];
    }
}
