<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertUserProductRequest extends FormRequest
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
            'ean' => ['required_without:users_stock_id', 'numeric'],
            'users_product_id' => ['required_without:ean', 'numeric'],
            'unit_weight' => [ 'nullable', 'numeric'],
            'amount' => ['required', 'numeric'],
            'price' => ['nullable', 'numeric'],
            'name' => ['nullable','alpha_dash'],
            'img' => ['nullable']
        ];
    }
}
