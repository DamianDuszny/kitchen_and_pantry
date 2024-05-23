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
     * @param recipes[] $recipes
     * @return void
     */
    public function createShoppingListFromRecipes(RecipesList $recipesList, Request $request): array {
        /** @var User $user */
        $user = auth('sanctum')->user();
        $neededProducts = (new ShoppingList($user))->transformRecipesListToShoppingList(
            $recipesList,
            (bool)$request->post('check_pantry'),
            array_count_values($request->post('recipes_ids'))
        );
        return $neededProducts;
    }
}
