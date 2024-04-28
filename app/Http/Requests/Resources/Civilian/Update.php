<?php

namespace App\Http\Requests\Resources\Civilian;

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
            'id' => 'required|string',
            'nik' => 'required|string|max:16',
            'fullName' => 'required|string',
            'birthplace' => 'required|string',
            'birthdate' => 'required|integer',
            'residentstatus' => 'required|string',
            'nkk' => 'required|integer',
            'isFamilyHead' => 'required|boolean',
            'rt_id' => 'required|integer',
            'address' => 'required|string',
            'status' => 'required|string',
            'phone' => 'required|string|max:20',
            'religion' => 'required|string',
            'job' => 'required|string',
        ];
    }
}
