<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRatesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency'      => 'required|string|min:3|max:3',
            'base_currency' => 'nullable|string|min:3|max:3|different:currency',
            'date'          => 'nullable|date|date_format:d/m/Y'
        ];
    }
}
