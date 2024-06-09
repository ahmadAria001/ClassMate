<?php

namespace App\Http\Requests\Resources\FA;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            'request_by' => 'required|integer|min:1',
            'childrens' => 'required|integer|min:1',
            'salary' => 'required|integer|min:1',
            'expenses' => 'required|integer|min:1',
            'job_status' => 'required|integer|min:1',
            'residence_status' => 'required|integer',
        ];
    }
}
