<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddRecipeSubstituteProductsRequest;
use App\Http\Requests\RecipeAccessRequest;
use App\Http\Requests\StoreRecipeRequest;
use App\Models\recipes;
use App\Models\recipes_products;
use App\Models\recipes_substitute_products;
use App\Models\user;
use Illuminate\Http\Request;

class RecipesController extends Controller
{
    public function index(Request $request) {
        /** @var user $user */
        $user = auth('sanctum')->user();
        return $user->recipes()->with('recipe_products')->get();
    }

    public function show(Request $request, string $id) {
        /** @var user $user */
        $user = auth('sanctum')->user();
        return $user->recipes()->with('recipe_products')->find($id);
    }

    public function store(StoreRecipeRequest $request) {
        try {
            $userProductsUsedInRecipe = $request->getUserProductsData();
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Recipe contains products that are not connected to account. You must add these products into your account',
                    'products_ids' => $e->getMessage()
                ],
                400
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

    public function addSubstitutes(AddRecipeSubstituteProductsRequest $request) {
        $recipesSubstituteProducts = recipes_substitute_products::where('recipe_id', '=', $request->post('products_id'))
            ->where('products_id', '=', $request->post('products_id'))->get();
        if(empty($recipesSubstituteProducts)) {
            $recipesSubstituteProducts = new recipes_substitute_products();
            $recipesSubstituteProducts->recipes_id = $request->post('recipe_id');
            $recipesSubstituteProducts->products_id = $request->post('products_id');
        }
        $recipesSubstituteProducts->substitute_for = $request->post('main_products_id');
        $recipesSubstituteProducts->comment = $request->post('comment');
        $recipesSubstituteProducts->weight = $request->post('weight');
        $recipesSubstituteProducts->amount = $request->post('amount');
        $recipesSubstituteProducts->how_well_fits = $request->post('how_well_fits');
        $recipesSubstituteProducts->save();
    }

    public function deleteSubstituteProduct(RecipeAccessRequest $request, string $recipeId, string $substituteProductId) {
        recipes_substitute_products::where('recipes_id', '=', $recipeId)->where('products_id', '=', $substituteProductId)->delete();
    }

    public function deleteRecipe(RecipeAccessRequest $request, string $recipeId) {
        recipes_substitute_products::where('recipes_id', '=', $recipeId)->delete();
        recipes_products::where('recipes_id', '=', $recipeId)->delete();
        recipes::find($recipeId)->delete();
    }
}
