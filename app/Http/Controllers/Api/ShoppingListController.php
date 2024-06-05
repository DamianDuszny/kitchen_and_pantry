<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingListAccessRequest;
use App\Models\recipes;
use App\Models\shopping_list;
use App\Models\user;
use App\Models\users_products_extra_data;
use App\Services\RecipesList;
use App\Services\ShoppingListCreator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function index(ShoppingListAccessRequest $request, ShoppingListCreator $shoppingList)
    {

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
    public function createShoppingListFromRecipesList(RecipesList $recipesList, Request $request): \Illuminate\Support\Collection
    {
        /** @var User $user */
        $user = auth('sanctum')->user();
        $shoppingListCreator = new ShoppingListCreator(
            $user,
            $recipesList,
            array_count_values($request->post('recipes_ids'))
        );
        $shoppingListCreator->createShoppingList((bool)$request->post('check_pantry'));
        return collect(
            [
                "shopping_list_data" => $shoppingListCreator->getShoppingList(),
                "pantry_data" => $shoppingListCreator->getPantryPantryProductsForProductsInShoppingList()
            ]
        );
    }

    public function editShoppingList(Request $request)
    {
        $productsAmounts = [];
    }

    public function approveShoppingList()
    {

    }

    public function createShoppingList(Request $request)
    {

    }
}
