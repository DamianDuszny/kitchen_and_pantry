<?php

namespace App\Services;

use App\Http\Requests\UpsertPantryStockRequest;
use App\Models\pantry;
use App\Models\products;
use App\Models\user;
use App\Models\pantry_stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PantryStockService
{
    protected \Illuminate\Contracts\Database\Eloquent\Builder $builder;
    private string $name;

    public function __construct(
        protected user $user,
        private readonly int $pantryId,
        private readonly ?int $pagination = 20,
        protected ?string $ean = null,
        protected ?int $stockId = null,
        protected ?array $expirationDate = null
    ) {}


    public function getUserProductStock() {
        return $this->setBaseBuilder()->getData();
    }

    public function findUserProductByEan($ean) {
        $this->ean = $ean;
        return $this->setPantryStockQueryBuilder()->getData();
    }

    public function findStockProductByName(string $name) {
        $this->name = $name;
        $this->setBaseBuilder();
        $this->addPantryProductLikeNameCondition();
        return $this->builder->get();
    }

    protected function getData() {
        if($this->pagination) {
            $this->builder->paginate($this->pagination);
        }
        return $this->builder->get();
    }

    protected function setPantryStockQueryBuilder(): self {
        $this->setBaseBuilder()
            ->addProductKeyConditionToBuilder();
        return $this;
    }

    protected function addProductKeyConditionToBuilder(): self {
        try {
            $this->addPantryProductsStockIdCondition();
        } catch (\Exception $e) {
            $this->addProductsEanCondition();
        }
        return $this->addExpirationDateCondition();
    }

    protected function addProductsEanCondition(): self {
        if(empty($this->getEan())) {
            throw new \Exception('No products ean');
        }
        $this->builder->whereHas(
            'products_ean',
            function (\Illuminate\Contracts\Database\Eloquent\Builder $builder) {
                $builder->where('ean', $this->getEan());
                return $builder;
            }
        );
        return $this;
    }

    protected function addPantryProductsStockIdCondition(): self {
        if(empty($this->getUserStockId())) {
            throw new \Exception('No product stock id');
        }
        $this->builder->where('id', $this->getUserStockId());
        return $this;
    }

    protected function addPantryProductLikeNameCondition(): self {
        if(empty($this->name)) {
            throw new \Exception('No product stock name');
        }
        $this->builder->where('description.name', 'LIKE', $this->name);
        return $this;
    }

    protected function setBaseBuilder(): self {
        $this->builder = pantry_stock::with('description', 'products_ean')->where('pantry_id', $this->getPantryId());

        return $this;
    }

    protected function getEan(): string {
        return $this->ean;
    }

    protected function getUserStockId(): int {
        return (int)$this->stockId;
    }

    protected function getPantryId(): int {
        return $this->pantryId;
    }

    private function addExpirationDateCondition(): self
    {
        if(count($this->expirationDate ?? []) > 1) {
            $this->builder->whereBetween('expiration_date', $this->expirationDate);
        } else {
            $this->builder->where('expiration_date', $this->expirationDate);
        }
        return $this;
    }
}
