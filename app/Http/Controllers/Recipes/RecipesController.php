<?php

namespace App\Http\Controllers\Recipes;

use App\Http\Controllers\Controller;
use App\Models\recipes;
use App\Models\recipes_products;
use App\Models\recipes_types;
use App\Models\user;
use Illuminate\Http\Request;

class RecipesController extends Controller
{
    public function index(Request $request) {
        echo 'index';
    }

    public function store(Request $request) {
        if(!recipes_types::find($request->post('recipe_type_id'))) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Given recipe type id doesnt exists in database',
                    'recipe_id' => $request->post('recipe_type_id')
                ]
            );
        }

        $productsIds = array_unique($request->post('products_ids'));
        /** @var user $user */
        $user = auth('sanctum')->user();
        $userProductsUsedInRecipe = $user->products()->whereIn('products_id', $productsIds)->get()->toArray();

        foreach($userProductsUsedInRecipe as $product) {
            unset($productsIds[array_search($product['pivot']['products_id'],$productsIds)]);
        }
        if(!empty($productsIds)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Recipe contains products that are not connected to account. You must add these products into your account',
                    'products_ids' => array_values($productsIds)
                ]
            );
        }
        $recipe = new recipes();
        $recipe->name = $request->post('recipe_name');
        $recipe->kcal = $request->post('kcal');
        $recipe->user_id = auth('sanctum')->id();
        $recipe->type = $request->post('recipe_type');
        $recipe->portion_for_how_many = $request->post('portion_for_how_many');
        $recipe->save();
        foreach($userProductsUsedInRecipe as $product) {
            $recipesProducts = new recipes_products();
            $recipesProducts->products_id = $product['pivot']['products_id'];
            $recipesProducts->recipes_id = $recipe->id;
            $recipesProducts->save();
        }
        return redirect('/recipe/'.$recipe->id);
    }
}
