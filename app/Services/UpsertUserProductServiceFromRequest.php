<?php

namespace App\Services;

use App\Http\Requests\UpsertUserProductRequest;
use App\Models\products;
use App\Models\users_products_descriptions;
use App\Models\users_products_stock;
use Illuminate\Contracts\Database\Eloquent\Builder;

class UpsertUserProductServiceFromRequest extends UserProductService
{
    public function __construct(
        private readonly UpsertUserProductRequest $requestData
    )
    {
        parent::__construct($this->requestData->user());
    }

    public function __invoke()
    {
        $productStockData = $this->setUserProductStockQueryBuilder()->getData();
        if ($productStockData->isEmpty()) {
            $productStockData = $this->createUserProductStockModel();
        } else {
            $productStockData = $productStockData[0];
        }
        $this->fillProductStockData($productStockData);
        $this->upsertProductDescription($productStockData);
        $this->upsertProductEan($productStockData);
        $productStockData->push();
        return $productStockData;
    }

    private function fillProductStockData(users_products_stock $productStockData)
    {
        $productStockData->expiration_date ??= $this->requestData['expiration_date'];
        $productStockData->amount += $this->requestData['amount'];
        $productStockData->net_weight += $this->requestData['net_weight'];
        $productStockData->unit_weight = $this->requestData['unit_weight'];
        $productStockData->price = $this->requestData['price'] * 100;
    }

    private function upsertProductDescription(users_products_stock $productStockData)
    {
        if (empty($productStockData->id)) {
            $productStockData->save();
        }
        if (empty($productStockData->description) || $this->requestData['name'] !== $productStockData->description->name) {
            $desc = $productStockData->description ?: new users_products_descriptions();
            $desc->name = $this->requestData['name'];
            $desc->users_products_stock_id = $productStockData->id;
            $desc->users_id = $this->user->id;
            $desc->img_url = '@todo';
            $desc->company = '@todo';
            $productStockData->setRelation('description', $desc);
        }
    }

    private function upsertProductEan(users_products_stock $productStockData)
    {
        if (empty($productStockData->products_ean) && $this->requestData['ean']) {
            $product = new products();
            $product->ean = $this->requestData['ean'];
            $product->save();
            $productStockData->products_id = $product->id;
            $productStockData->setRelation('products_ean', $product);
        }
    }

    protected function getUserStockId(): int
    {
        return $this->requestData['users_stock_id'] ?? 0;
    }

    private function createUserProductStockModel()
    {
        $model = new users_products_stock();
        $model->users_id = $this->user->id;
        return $model;
    }

    protected function getEan(): string
    {
        return $this->requestData['ean'] ?? '';
    }

    protected function addAdditionalConditionToBuilder()
    {
        $this->builder->where('expiration_date', $this->requestData['expiration_date'] ?: null);
    }
}
