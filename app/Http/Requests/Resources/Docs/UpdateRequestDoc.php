<?php

namespace App\Http\Requests\Resources\Docs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestDoc extends FormRequest
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
            'requestStatus' => 'required|string',
            'request_by'=>'nullable|integer',
            //
            'description' => 'string',
        ];
    }
}
