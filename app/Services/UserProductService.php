<?php

namespace App\Services;

use App\Http\Requests\UpsertUserProductRequest;
use App\Models\products;
use App\Models\user;
use App\Models\users_products_stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserProductService
{
    protected ?string $ean = null;
    protected \Illuminate\Contracts\Database\Eloquent\Builder $builder;
    public function __construct(
        protected user $user,
        private readonly ?int $pagination = null
    ) {}


    public function getUserProductStock() {
        return $this->setBaseBuilder()->getData();
    }

    public function findUserProductByEan($ean) {
        $this->ean = $ean;
        return $this->setUserProductStockQueryBuilder()->getData();
    }

    protected function getData() {
        if($this->pagination) {
            $this->builder->paginate($this->pagination);
        }
        return $this->builder->get();
    }

    protected function setUserProductStockQueryBuilder(): self {
        $this->setBaseBuilder()
            ->addProductKeyConditionToBuilder()
            ->addAdditionalConditionToBuilder();
        return $this;
    }

    protected function addProductKeyConditionToBuilder(): self {
        try {
            $this->addUsersProductsStockIdCondition();
        } catch (\Exception $e) {
            $this->addProductsEanCondition();
        }
        return $this;
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

    protected function addUsersProductsStockIdCondition(): self {
        if(empty($this->getUserStockId())) {
            throw new \Exception('No users stock id');
        }
        $this->builder->whereHas(
            'users_products_stock',
            function (Builder $builder) {
                $builder->where('id', $this->getUserStockId());
                return $builder;
            }
        );
        return $this;
    }

    protected function setBaseBuilder(): self {
        $this->builder = $this->user->products_stock()->with(['description', 'products_ean']);
        return $this;
    }

    protected function getEan(): string {
        return '';
    }

    protected function getUserStockId(): int {
        return 0;
    }

    protected function addAdditionalConditionToBuilder(){}
}
