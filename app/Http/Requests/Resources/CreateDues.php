<?php

namespace App\Http\Requests\Resources;

use Illuminate\Foundation\Http\FormRequest;

class CreateDues extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'typeDues' => 'required|string',
            'description' => 'required|string',
            'amt_dues' => 'required|numeric',
            'amt_fund' => 'required|numeric',
            'status' => 'required|boolean',
            'rt_id' => 'required|integer',
        ];
    }
}
