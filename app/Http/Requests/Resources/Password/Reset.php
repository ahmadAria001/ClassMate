<?php

namespace App\Http\Requests\Resources\Password;

use Illuminate\Foundation\Http\FormRequest;

class Reset extends FormRequest
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
            'old_password' => 'required|min:1|string',
            'new_password' => ['required', 'min:8', 'string', 'regex:/[~!@#\$%\^&\*\(\)_\+<>?:"{}\|,\.\/;\'\[\]\\\]/'],
        ];
    }
}