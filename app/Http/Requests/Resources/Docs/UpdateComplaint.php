<?php

namespace App\Http\Requests\Resources\Docs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaint extends FormRequest
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
            'complaintStatus' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            //Docs Attr
            'description' => 'string',//Type Fixed on Controller
        ];
    }
}
