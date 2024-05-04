<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecipeRequest;
use App\Models\recipes;
use App\Models\recipes_products;
use Illuminate\Http\Request;

class RecipesController extends Controller
{
    public function index(Request $request) {
        echo 'index';
    }

    public function store(StoreRecipeRequest $request) {
        try {
            $userProductsUsedInRecipe = $request->getUserProductsData();
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Recipe contains products that are not connected to account. You must add these products into your account',
                    'products_ids' => $e->getMessage()
                ]
            );
        }
        $recipe = new recipes();
        $recipe->name = $request->post('recipe_name');
        $recipe->kcal = $request->post('kcal');
        $recipe->user_id = auth('sanctum')->id();
        $recipe->type = $request->post('recipe_type_id');
        $recipe->portion_for_how_many = $request->post('portion_for_how_many');
        $recipe->description = $request->post('description');
        $recipe->instruction = $request->post('instruction');
        $recipe->preparation_time = $request->post('preparation_time');
        $recipe->complexity = $request->post('complexity');
        $recipe->save();
        foreach($userProductsUsedInRecipe as $product) {
            $recipesProducts = new recipes_products();
            $recipesProducts->products_id = $product['pivot']['products_id'];
            $recipesProducts->recipes_id = $recipe->id;
            $recipesProducts->how_well_matches = 100;
            $recipesProducts->amount = $request->post('products_ids_to_amounts')[$product['pivot']['products_id']];
            $recipesProducts->save();
        }
        return redirect('/recipe/'.$recipe->id);
    }
}
