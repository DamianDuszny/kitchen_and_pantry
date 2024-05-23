<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingListAccessRequest;
use App\Models\recipes;
use App\Models\user;
use App\Services\RecipesList;
use App\Services\ShoppingList;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function index(ShoppingListAccessRequest $request, ShoppingList $shoppingList) {

    }

    /**
     * Creating needed products for given recipes. Recipes must be connected to current logged user.
     * Recipes are loaded with dependency injection using post array variable 'recipes_id' {@see \App\Providers\AppServiceProvider::register}.
     * Endpoint can check user pantry for this operation, if is needed just use post variable 'check_pantry' [0,1]
     *
     * @param RecipesList $recipesList auto added using post array variable recipes_ids
     * @param Request $request
     * @return array
     */
    public function createShoppingListFromRecipes(RecipesList $recipesList, Request $request): array {
        /** @var User $user */
        $user = auth('sanctum')->user();
        return (new ShoppingList($user))->transformRecipesListToShoppingList(
            $recipesList,
            (bool)$request->post('check_pantry'),
            array_count_values($request->post('recipes_ids'))
        );
    }
}
