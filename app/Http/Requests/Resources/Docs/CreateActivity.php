<?php

namespace App\Http\Requests\Resources\Docs;

use Illuminate\Foundation\Http\FormRequest;

class CreateActivity extends FormRequest
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
            'name' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'location' => 'required|string',
            //Docs Attr
            'description' => 'string',
        ];
    }
}
