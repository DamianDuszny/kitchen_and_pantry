<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PantryProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ean' => ['numeric', 'required_without:users_product_id'],
            'users_product_id' => ['numeric', 'required_without:users_product_id'],
            'unit_weight' => ['numeric', 'nullable'],
            'amount' => ['numeric', 'required'],
            'price' => ['numeric', 'nullable'],
            'name' => ['required']
        ];
    }
}
