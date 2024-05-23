<?php

namespace App\Services;

use App\Models\shopping_list;
use App\Models\user;

class ShoppingList
{
    private shopping_list $shopping_list;

    private array $userPantryProductsChangable;

    private readonly array $userPantryProducts;

    public function __construct(private readonly User $user)
    {
    }

    /**
     * Returns array:
     * {
     *  products_id: {
     *      recipes_id: {
     *          "products_id": products_id - id from products table,
     *          "main_amount_needed": int - recipe needed product amount - pantry products (if argument checkPantry is set to true),
     *          "recipes_id": int - recipes_id from users_recipes table,
     *          "substitute_product_id": int - id from products table,
     *          "main_amount_in_pantry": int - main recipe product amount in pantry,
     *          "substitute_amount_in_pantry": int - substitute recipe product amount in pantry,
     *          "substitute_amount_needed": int - recipe substitute for main product needed amount - pantry products (if argument checkPantry is set to true)
     *      }
     *  }
     * }
     *
     * @param RecipesList $recipesList
     * @param bool $checkPantry if is set to true, user pantry products amount will be checked and subtracted from needed
     * product amount for recipe. Main products have priority over substitute products.
     * @param array $multipleRecipes
     * @return array
     */

    public function transformRecipesListToShoppingList(RecipesList $recipesList, bool $checkPantry = true, array $multipleRecipes = []): array
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
            foreach ($recipesProducts as $product) {
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

    private function findSubstitutesForProductInRecipesList(RecipesList $recipesList, array $mainProduct): array
    {
        foreach ($recipesList->getProductsFromRecipes()['substitute_products'] as $product) {
            if ($product['substitute_for'] === $mainProduct['products_id'] && $product['recipes_id'] === $mainProduct['recipes_id']) {
                return $product;
            }
        }
        return [];
    }

    /**
     * Sets two properties {@see userPantryProducts} and {@see userPantryProductsChangable}.
     *
     * @param $productsIds
     * @return void
     */
    private function setUserPantryProducts($productsIds): void
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

    /**
     * Calculate needed amount for product. If checkPantry is set to true will subtract amount from {@see userPantryProductsChangable}
     *
     * @param array $product
     * @param bool $checkPantry
     * @param bool $subtractAmountFromPantry
     * @return int
     */
    private function getNeededAmountForProduct(array $product, bool $checkPantry = true, bool $subtractAmountFromPantry = true): int
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
