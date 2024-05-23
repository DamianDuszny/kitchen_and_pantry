<?php

namespace App\Services;

use App\Models\shopping_list;
use App\Models\user;

class ShoppingList
{
    private shopping_list $shopping_list;

    private array $userPantryProductsChangable;

    private array $userPantryProducts;

    public function __construct(private User $user){}

    public function transformRecipesListToShoppingList(RecipesList $recipesList, bool $checkPantry = true, array $multipleRecipes = [])
    {
        if ($checkPantry) {
            $this->setUserPantryProducts(
                array_merge(
                    array_column($recipesList->getProductsFromRecipes()['main_products'], 'id'),
                    array_column($recipesList->getProductsFromRecipes()['substitute_products'], 'id'),
                )
            );
        }
        $neededProducts = [];
        foreach ($recipesList->getProductsFromRecipes()['main_products'] as $product) {
            for ($i = 0; $i <= $multipleRecipes[$product['recipes_id']] ?? 0; $i++) {
                $neededAmount = $this->getNeededAmountForProduct($product, $checkPantry);
                if (empty($neededProducts[$product['products_id']][$product['recipes_id']])) {
                    $neededProducts[$product['products_id']][$product['recipes_id']] = [
                        'products_id' => $product['products_id'],
                        'main_amount_needed' => $neededAmount,
                        'recipes_id' => $product['recipes_id']
                    ];
                } else {
                    $neededProducts[$product['products_id']][$product['recipes_id']]['main_amount_needed'] += $neededAmount;
                }
            }
        }

        foreach ($neededProducts as $recipesProducts) {
            foreach($recipesProducts as $product) {
                $substitute = $this->findSubstitutesForProductInRecipesList($recipesList, $product);
                if (empty($substitute)) {
                    continue;
                }
                $neededProducts[$product['products_id']][$product['recipes_id']]['substitute_product_id'] = $substitute['products_id'];
                if ($checkPantry) {
                    $neededProducts[$product['products_id']][$product['recipes_id']]['main_amount_in_pantry'] = $this->userPantryProducts[$product['products_id']]['amount'];
                    $neededProducts[$product['products_id']][$product['recipes_id']]['substitute_amount_in_pantry'] = $this->userPantryProducts[$substitute['products_id']]['amount'];
                }
                $neededProducts[$product['products_id']][$product['recipes_id']]['substitute_amount_needed'] = $this->getNeededAmountForProduct($substitute, $checkPantry, false);
            }
        }

        return $neededProducts;
    }

    private function findSubstitutesForProductInRecipesList(RecipesList $recipesList, array $mainProduct)
    {
        foreach ($recipesList->getProductsFromRecipes()['substitute_products'] as $product) {
            if ($product['substitute_for'] === $mainProduct['products_id'] && $product['recipes_id'] === $mainProduct['recipes_id']) {
                return $product;
            }
        }
        return [];
    }

    private function setUserPantryProducts($productsIds)
    {
        $this->userPantryProducts = $this->user
            ->products()
            ->with('description')
            ->select('products_id')
            ->whereIn('products_id', $productsIds)
            ->groupBy('products_id', 'users_id')
            ->selectRaw('SUM(amount) AS amount, SUM(net_weight) AS net_weight, MIN(unit_weight) as unit_weight')
            ->get()
            ->keyBy('products_id')
            ->toArray();

        $this->userPantryProductsChangable = $this->userPantryProducts;
    }

    private function getNeededAmountForProduct(array $product, bool $checkPantry = true, bool $subtractAmountFromPantry = true)
    {
        if (!$checkPantry) {
            return $product['amount'];
        }
        $productAmountInPantry = $this->userPantryProductsChangable[$product['products_id']]['amount'] ?? 0;
        if ($product['amount'] >= $productAmountInPantry) {
            if ($subtractAmountFromPantry) {
                $this->userPantryProductsChangable[$product['products_id']]['amount'] = 0;
            }
            $neededAmount = $product['amount'] - $productAmountInPantry;
        } else {
            if ($subtractAmountFromPantry) {
                $this->userPantryProductsChangable[$product['products_id']]['amount'] -= $product['amount'];
            }
            $neededAmount = 0;
        }

        return $neededAmount;
    }
}
