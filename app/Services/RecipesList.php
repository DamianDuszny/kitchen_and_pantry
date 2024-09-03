<?php

namespace App\Services;

use App\DTO\RecipeProduct;
use App\DTO\RecipesProducts;
use App\Models\recipes;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Http\Client\Request;

class RecipesList
{
    private \Illuminate\Database\Eloquent\Collection $modelsList;

    public function __construct(array $modelsIds, ?int $userId = null)
    {
        //limited to 20 recipes
        $modelsIds = array_slice($modelsIds, 0,20);
        $sq = recipes::with(['recipe_products', 'recipe_substitute_products'])->whereIn('id', $modelsIds);
        if (!is_null($userId)) {
            recipes::userRecipe($sq, $userId);
        }
        $this->modelsList = $sq->get();
    }

    public function getProductsFromRecipes(): RecipesProducts
    {
        $recipesProducts = new RecipesProducts();
        /** @var recipes $model */
        foreach ($this->modelsList as $model) {
            foreach ($model->recipe_products as $product) {
                $recipesProducts->addMainProduct(new RecipeProduct($product['recipes_id'], $product['products_id'], $product['amount']));
            }
            foreach ($model->recipe_substitute_products as $product) {
                $recipesProducts->addSubstituteProduct(new RecipeProduct($product['recipes_id'], $product['products_id'], $product['amount'], $product['substitute_for']));
            }
        }
        return $recipesProducts;
    }

    public function getRecipesIds(): array {
        return $this->modelsList->modelKeys();
    }
}
