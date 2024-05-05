<?php

namespace App\Rules;

use App\Models\recipes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductExistsInRecipeRule implements ValidationRule
{
    public function __construct(private int $recipeId) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $recipeWithProducts = recipes::with('recipe_products')->find($this->recipeId);
        foreach($recipeWithProducts->recipe_products as $productData) {
            if($productData->products_id == $value) {
                return;
            }
        }
        $fail("Product with id $value not found in recipe with id $this->recipeId");
    }
}
