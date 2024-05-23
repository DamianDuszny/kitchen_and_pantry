<?php

namespace App\ModelsLists;

use App\Models\recipes;

class RecipesList
{
    private \Illuminate\Database\Eloquent\Collection $modelsList;

    public function __construct(array $ModelsIds, ?int $userId = null)
    {
        //limited to 20 recipes
        $ModelsIds = array_slice($ModelsIds, 0,20);
        $sq = recipes::with(['recipe_products', 'recipe_substitute_products'])->whereIn('id', $ModelsIds);
        if (!is_null($userId)) {
            recipes::userRecipe($sq, $userId);
        }
        $this->modelsList = $sq->get();
    }

    public function getList(): array
    {
        return $this->modelsList;
    }

    public function getProductsFromRecipes(): array
    {
        $products = [];

        /** @var recipes $model */
        foreach ($this->modelsList as $model) {
            $products['main_products'] = array_merge($products['main_products'] ?? [], $model->recipe_products->toArray());
            $products['substitute_products'] = array_merge($products['substitute_products'] ?? [] , $model->recipe_substitute_products->toArray());
        }
        return $products;
    }
}
