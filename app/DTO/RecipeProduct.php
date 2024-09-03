<?php
namespace App\DTO;
class RecipeProduct
{
    public function __construct(
        public readonly int  $recipesId,
        public readonly int  $productsId,
        public ?int  $amount,
        public readonly ?int $substituteFor = null,
        public ?int $weight = null
    ) {}

}
