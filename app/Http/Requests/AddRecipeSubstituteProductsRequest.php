<?php

namespace App\Http\Requests;

use App\Models\recipes;
use App\Rules\ProductExistsInRecipeRule;
use Illuminate\Foundation\Http\FormRequest;

class AddRecipeSubstituteProductsRequest extends RecipeAccessRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipe_id' => ['required', 'int', 'exists:recipes,id'],
            'main_products_id' => ['required', 'int', 'exists:products,id', new ProductExistsInRecipeRule($this->get('recipe_id'))],
            'products_id' => ['required', 'int', 'exists:products,id'],
            'amount' => ['int'],
            'weight' => ['int'],
            'comment' => ['string', 'max:5000'],
            'how_well_fits' => ['int', 'between:1,100', 'required']
        ];
    }
}
