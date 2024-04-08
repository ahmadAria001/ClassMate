<?php

namespace App\Http\Requests\Resources\FA;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            'id' => 'required|integer|min:1',
            'request_by' => 'required|integer|min:1',
            'tanggungan' => 'required|integer|min:1',
            'alasan' => 'required|string',
            'status' => 'string',
        ];
    }
}
