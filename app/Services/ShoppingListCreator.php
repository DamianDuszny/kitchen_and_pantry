<?php

namespace App\Services;

use App\Models\shopping_list;
use App\Models\shopping_list_products;
use App\Models\user;

class ShoppingListCreator
{
    private array $userPantryProductsChangable;

    private readonly array $userPantryProducts;

    private readonly shopping_list $shopping_list;

    public function __construct(
        private readonly User $user,
        private readonly RecipesList $recipesList,
        private readonly array $multipleRecipes
    )
    {
        $this->shopping_list = new shopping_list(['users_id'=>$this->user->id]);
        $this->shopping_list->save();
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
     * @param RecipesList $this->recipesList
     * @param bool $checkPantry if is set to true, user pantry products amount will be checked and subtracted from needed
     * product amount for recipe. Main products have priority over substitute products.
     * @return array
     */

    public function getNeededProductsAmountsFromRecipesList(bool $checkPantry = true): array
    {
        //@todo weight too...
        if ($checkPantry) {
            $this->setUserPantryProducts(
                array_merge(
                    array_column($this->recipesList->getProductsFromRecipes()['main_products'], 'id'),
                    array_column($this->recipesList->getProductsFromRecipes()['substitute_products'], 'id'),
                )
            );
        }
        $neededProducts = $this->getNeededMainProducts($checkPantry);
        $this->saveShoppingListProducts($neededProducts);
        $nedeedSubstituteProducts = $this->getSubstituteProductsForMainProducts($neededProducts, $checkPantry);
        $this->saveShoppingListProducts($nedeedSubstituteProducts);
        return $nedeedSubstituteProducts;
    }

    /**
     * @param array $shoppingListProducts
     * @return void
     */
    private function saveShoppingListProducts(array $shoppingListProducts) {
        shopping_list_products::insert(
            array_filter(
                array_map(
                    fn ($x) => $x->amount > 0 ? $x->toArray() : null,
                    array_merge(...$shoppingListProducts)
                )
            )
        );
    }

    /**
     * @param bool $checkPantry
     * @return shopping_list[]
     */
    private function getNeededMainProducts(bool $checkPantry = true): array {
        $shoppingListProducts = [];
        foreach ($this->recipesList->getProductsFromRecipes()['main_products'] as $product) {
            for ($i = 0; $i <= $this->multipleRecipes[$product['recipes_id']] ?? 0; $i++) {
                $neededAmount = $this->getNeededAmountForProduct($product, $checkPantry);
                if (empty($shoppingListProducts[$product['products_id']][$product['recipes_id']])) {
                    $shoppingListProduct = $this->getShoppingListProduct(
                        productsId: $product['products_id'],
                        recipesId: $product['recipes_id'],
                        amount: $neededAmount
                    );
                    $shoppingListProducts[$product['products_id']][$product['recipes_id']] = $shoppingListProduct;
                } else {
                    $shoppingListProducts[$product['products_id']][$product['recipes_id']]->amount += $neededAmount;
                }
            }
        }
        return $shoppingListProducts;
    }

    /**
     * @param array $neededProducts
     * @param bool $checkPantry
     * @return shopping_list_products[]
     */
    private function getSubstituteProductsForMainProducts(array $neededProducts, bool $checkPantry = true): array {
        $neededSubstituteProducts = [];
        foreach ($neededProducts as $recipesProducts) {
            /** @var shopping_list_products $mainShoppingListProduct */
            foreach ($recipesProducts as $mainShoppingListProduct) {
                $substitute = $this->findSubstitutesForProductInRecipesList($mainShoppingListProduct);
                if (empty($substitute)) {
                    continue;
                }
                $shoppingListProduct = $this->getShoppingListProduct(
                    productsId: $substitute['products_id'],
                    recipesId: $mainShoppingListProduct->recipes_id,
                    amount: $this->getNeededAmountForProduct($substitute, $checkPantry, false),
                    substituteFor: $mainShoppingListProduct->id
                );
                $neededSubstituteProducts[$substitute['products_id']][$mainShoppingListProduct['recipes_id']] = $shoppingListProduct;
            }
        }
        return $neededSubstituteProducts;
    }

    /**
     * @param array $data
     * @return shopping_list_products
     */
    private function getShoppingListProduct(int $productsId, int $recipesId, ?int $amount = null, int $weight = null, ?int $substituteFor = null): shopping_list_products {
        $shoppingListProduct = new shopping_list_products();
        $shoppingListProduct->shopping_lists_id = $this->shopping_list->id;
        $shoppingListProduct->products_id = $productsId;
        $shoppingListProduct->recipes_id = $recipesId;
        $shoppingListProduct->amount = $amount;
        $shoppingListProduct->weight = $weight;
        $shoppingListProduct->substitute_for = $substituteFor;
        return $shoppingListProduct;
    }

    private function findSubstitutesForProductInRecipesList(shopping_list_products $mainProduct): array
    {
        foreach ($this->recipesList->getProductsFromRecipes()['substitute_products'] as $product) {
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
