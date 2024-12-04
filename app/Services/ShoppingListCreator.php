<?php

namespace App\Services;

use App\DTO\RecipeProduct;
use App\Models\shopping_list;
use App\Models\shopping_list_products;
use App\Models\user;
use App\Models\pantry_stock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ShoppingListCreator
{
    private Collection $userPantryProductsChangeable;

    private readonly Collection $userPantryProducts;

    private readonly shopping_list $shoppingList;

    public function __construct(
        private readonly User        $user,
        private readonly RecipesList $recipesList,
        private readonly array       $multipleRecipes
    )
    {
        $this->shoppingList = new shopping_list(
            [
                'users_id' => $this->user->id,
                'note' => 'created from recipes: ' . implode($this->recipesList->getRecipesIds())
            ]
        );
        $this->shoppingList->save();
    }

    /**
     * @param bool $checkPantry if is set to true, user pantry products amount will be checked and subtracted from needed
     * product amount for recipe. Main products have priority over substitute products.
     */

    public function createShoppingList(bool $checkPantry = true): bool
    {
        //@todo weight too...
        if ($checkPantry) {
            $products = $this->recipesList->getProductsFromRecipes();
            $this->setUserPantryProducts(
                array_merge(
                    array_column($products->mainProducts, 'productsId'),
                    array_column($products->substituteProducts, 'productsId'),
                )
            );
        }

        $neededProducts = $this->getNeededMainProducts($checkPantry);
        $this->saveShoppingListProducts($neededProducts);
        $this->shoppingList->load('shoppingListProducts');
        $neededSubstituteProducts = $this->getSubstituteProductsForMainProducts($this->shoppingList->shoppingListProducts, $checkPantry);
        $this->saveShoppingListProducts($neededSubstituteProducts);
        $this->shoppingList->setRelation(
            'shoppingListProducts',
            $this->shoppingList->shoppingListProducts->merge(collect($neededSubstituteProducts))
        );
        return true;
    }

    /**
     * @param array $shoppingListProducts
     * @return void
     */
    private function saveShoppingListProducts(array $shoppingListProducts)
    {
        shopping_list_products::insert(
            array_filter(
                array_map(
                    fn($x) => $x->toArray(),
                    $shoppingListProducts
                )
            )
        );
    }

    /**
     * @param bool $checkPantry
     * @return shopping_list[]
     */
    private function getNeededMainProducts(bool $checkPantry = true): array
    {
        $shoppingListProducts = [];
        foreach ($this->recipesList->getProductsFromRecipes()->mainProducts as $product) {
            for ($i = 0; $i <= $this->multipleRecipes[$product->recipesId] ?? 0; $i++) {
                $neededAmount = $this->getNeededAmountForProduct($product, $checkPantry);
                if (empty($shoppingListProducts[$product->productsId][$product->productsId])) {
                    $shoppingListProduct = $this->getShoppingListProduct(
                        productsId: $product->productsId,
                        recipesId: $product->recipesId,
                        amount: $neededAmount
                    );
                    $shoppingListProducts[$product->productsId][$product->recipesId] = $shoppingListProduct;
                } else {
                    $shoppingListProducts[$product->productsId][$product->recipesId]->amount += $neededAmount;
                }
            }
        }
        return array_merge(...$shoppingListProducts);
    }

    /**
     * @param Collection $mainProducts - collection of {@see shopping_list_products}
     * @param bool $checkPantry
     * @return shopping_list_products[]
     */
    private function getSubstituteProductsForMainProducts(Collection $mainProducts, bool $checkPantry = true): array
    {
        $neededSubstituteProducts = [];
        /** @var shopping_list_products $mainShoppingListProduct */
        foreach ($mainProducts as $mainShoppingListProduct) {
            $substitute = $this->findSubstitutesForProductInRecipesList($mainShoppingListProduct);
            if (empty($substitute->productsId)) {
                continue;
            }
            //if null use amount of main product
            $substitute->amount ??= $mainShoppingListProduct->amount;
            $substitute->weight ??= $mainShoppingListProduct->weight;
            if (empty($neededSubstituteProducts[$substitute->productsId][$mainShoppingListProduct->recipes_id])) {
                $shoppingListProduct = $this->getShoppingListProduct(
                    productsId: $substitute->productsId,
                    recipesId: $mainShoppingListProduct->recipes_id,
                    amount: $this->getNeededAmountForProduct($substitute, $checkPantry, false),
                    substituteFor: $mainShoppingListProduct->id,
                    accepted: false
                );
                $neededSubstituteProducts[$substitute->productsId][$mainShoppingListProduct->recipes_id] = $shoppingListProduct;
            } else {
                $neededSubstituteProducts[$substitute->productsId][$mainShoppingListProduct->recipes_id]->amount += $this->getNeededAmountForProduct($substitute, $checkPantry, false);
            }

        }
        return array_merge(...$neededSubstituteProducts);
    }

    /**
     * @param array $data
     * @return shopping_list_products
     */
    private function getShoppingListProduct(int $productsId, int $recipesId, ?int $amount = null, int $weight = null, ?int $substituteFor = null, ?bool $accepted = true): shopping_list_products
    {
        $shoppingListProduct = new shopping_list_products();
        $shoppingListProduct->shopping_lists_id = $this->shoppingList->id;
        $shoppingListProduct->products_id = $productsId;
        $shoppingListProduct->recipes_id = $recipesId;
        $shoppingListProduct->amount = $amount;
        $shoppingListProduct->weight = $weight;
        $shoppingListProduct->substitute_for = $substituteFor;
        $shoppingListProduct->accepted = $accepted;
        return $shoppingListProduct;
    }

    /**
     * @param shopping_list_products $mainProduct
     * @return array
     */
    private function findSubstitutesForProductInRecipesList(shopping_list_products $mainProduct): RecipeProduct
    {
        foreach ($this->recipesList->getProductsFromRecipes()->substituteProducts as $product) {
            if ($product->substituteFor === $mainProduct['products_id'] && $product->recipesId === $mainProduct['recipes_id']) {
                return $product;
            }
        }
        return new RecipeProduct(0,0, null);
    }

    /**
     * Sets two properties {@see userPantryProducts} and {@see userPantryProductsChangeable}.
     *
     * @param $productsIds
     * @return void
     */
    private function setUserPantryProducts($productsIds): void
    {
        $this->userPantryProducts = $this->user
            ->products_stock()
            ->with('description')
            ->select('products_id')
            ->whereIn('products_id', $productsIds)
            ->groupBy('products_id', 'users_id')
            ->selectRaw('SUM(amount) AS amount, SUM(net_weight) AS net_weight, MIN(unit_weight) as unit_weight')
            ->get()
            ->keyBy('products_id');
        $this->userPantryProductsChangeable = $this->userPantryProducts;
    }

    /**
     * Calculate needed amount for product. If checkPantry is set to true will subtract amount from {@see userPantryProductsChangeable}
     *
     * @param RecipeProduct $product
     * @param bool $checkPantry
     * @param bool $subtractAmountFromPantry
     * @return int
     */
    private function getNeededAmountForProduct(RecipeProduct $product, bool $checkPantry = true, bool $subtractAmountFromPantry = true): int
    {
        if (!$checkPantry || !isset($this->userPantryProductsChangeable[$product->productsId])) {
            return $product->amount;
        }

        $productAmountInPantry = $this->userPantryProductsChangeable[$product->productsId]['amount'] ?? 0;
        if ($product->amount >= $productAmountInPantry) {
            if ($subtractAmountFromPantry) {
                $this->userPantryProductsChangeable[$product->productsId]['amount'] = 0;
            }
            $neededAmount = $product->amount - $productAmountInPantry;
        } else {
            if ($subtractAmountFromPantry) {
                $this->userPantryProductsChangeable[$product->productsId]['amount'] -= $product->amount;
            }
            $neededAmount = 0;
        }

        return $neededAmount;
    }

    public function getShoppingList(): shopping_list {
        return $this->shoppingList;
    }

    public function getPantryPantryProductsForProductsInShoppingList(): array {
        return $this->userPantryProducts->whereIn('products_id', $this->shoppingList->shoppingListProducts->pluck('products_id'))->toArray();
    }
}
