<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Field user_id wajib diisi',
            'user_id.integer' => 'Field user_id harus berupa angka',
            'user_id.min' => 'Field user_id minimal bernilai 1',
        ];
    }
}
