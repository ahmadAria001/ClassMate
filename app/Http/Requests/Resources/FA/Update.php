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
            'request_by' => 'integer|min:1',
            'childrens' => 'integer|min:1',
            'salary' => 'integer|min:1',
            'expenses' => 'integer|min:1',
            'job_status' => 'integer|min:1',
            'residence_status' => 'integer|min:1',
            'status' => 'integer',
        ];
    }
}
