<?php

namespace App\Http\Requests;

use App\Dictionary\PantryRoles\PantryRole;

class UpsertPantryStockRequest extends PantryRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'ean' => ['required_without:pantry_stock_id', 'numeric', 'nullable'],
            'pantry_stock_id' => ['required_without:ean', 'numeric'],
            'unit_weight' => [ 'nullable', 'numeric'],
            'amount' => ['required', 'numeric'],
            'price' => ['nullable', 'numeric'],
            'name' => ['nullable','regex:/^[A-Za-z0-9\s_-]+$/'],
            'img' => ['nullable']
        ], parent::rules());
    }

    protected function getNeededPermissionLevel(): array {
        return PantryRole::rolesWithWritePerm();
    }
}
