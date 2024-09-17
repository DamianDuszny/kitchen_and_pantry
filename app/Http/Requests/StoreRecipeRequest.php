<?php

namespace App\Http\Requests;

use App\Models\user;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipe_name' => ['max:20', 'min:3', 'required'],
            'description' => ['string', 'required', 'max:5000'],
            'instruction' => ['string', 'required', 'max:5000'],
            'preparation_time' => ['int', 'required'],
            'recipe_type_id' => ['int', 'required', 'exists:recipes_types,id'],
            'kcal' => ['int'],
            'portion_for_how_many' => ['int'],
            'complexity' => ['int'],
        ];
    }
//@todo move to service
    public function getUserProductsData() {
        $productsIds = array_keys($this->post('products_ids_to_amounts'));
        /** @var user $user */
        $user = auth('sanctum')->user();
        $userProductsUsedInRecipe = $user->products_stock()->whereIn('products_id', $productsIds)->get()->toArray();

        foreach($userProductsUsedInRecipe as $product) {
            unset($productsIds[array_search($product['pivot']['products_id'],$productsIds)]);
        }
        if(!empty($productsIds)) {
            throw new \Exception(implode(',', array_values($productsIds)));
        }
        return $userProductsUsedInRecipe;
    }
}
