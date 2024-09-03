<?php
namespace App\DTO;
class RecipesProducts
{
    /** @var RecipeProduct[] $mainProducts */
    public array $mainProducts = [];

    /** @var RecipeProduct[] $substituteProducts */
    public array $substituteProducts = [];

    public function addMainProduct(RecipeProduct $product): void {
        $this->mainProducts[] = $product;
    }

    public function addSubstituteProduct(RecipeProduct $product): void {
        $this->substituteProducts[] = $product;
    }

}
