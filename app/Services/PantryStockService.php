<?php

namespace App\Services;

use App\Models\user;
use App\Models\pantry_stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Exceptions\PantryStockService\NoDataSentException;

class PantryStockService
{
    protected Builder $builder;
    private string $name;

    public function __construct(
        protected user $user,
        private readonly null|array|int $pantryId,
        private readonly ?int $pagination = 20,
        protected ?string $ean = null,
        protected ?int $stockId = null,
        protected ?array $expirationDate = null
    ) {}


    public function getUserProductStock(): Collection {
        return $this->setBaseBuilder()->getData();
    }

    public function findUserProductByEan($ean): Collection {
        $this->ean = $ean;
        $this->setPantryStockQueryBuilder();
        return $this->getData();
    }

    public function findStockProductByName(string $name): Collection {
        $this->name = $name;
        $this->setBaseBuilder();
        $this->addPantryProductLikeNameCondition();
        return $this->getData();
    }

    protected function getData(): Collection {
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
        } catch (NoDataSentException) {
            $this->addProductsEanCondition();
        }
        return $this;//->addExpirationDateCondition();
    }

    protected function addProductsEanCondition(): self {
        if(empty($this->getEan())) {//@todo cannot unset ean from record :(
            throw new \Exception('No products ean');
        }
        $this->builder->whereHas(
            'products_ean',
            function (Builder $builder) {
                $builder->where('ean', $this->getEan());
                return $builder;
            }
        );
//        echo \Illuminate\Support\Str::replaceArray('?', $this->builder->getBindings(), $this->builder->toSql());die;
        return $this;
    }

    protected function addPantryProductsStockIdCondition(): self {
        if(empty($this->getUserStockId())) {
            throw new NoDataSentException('No product stock id');
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

    protected function setBaseBuilder(): self {//@todo może załadować pantry wraz z produktami itd
        $this->builder = pantry_stock::with('description', 'products_ean', 'pantry.users');
        if($this->pantryId === null) {
            $this->builder->whereHas('pantry.users', function (\Illuminate\Contracts\Database\Eloquent\Builder $builder) {
                $builder->where('users_id', $this->user->id);
                return $builder;
            });
        } elseif(is_numeric($this->pantryId)) {
            $this->builder->where('pantry_id', $this->pantryId);
        } else {
            $this->builder->whereIn('pantryId', $this->pantryId);
        }

        return $this;
    }

    protected function getEan(): string {
        return $this->ean;
    }

    protected function getUserStockId(): int {
        return (int)$this->stockId;
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
