<?php

namespace App\Services;

use App\Exceptions\DuplicateRecord;
use App\Http\Requests\UpsertPantryStockRequest;
use App\Models\products;
use App\Models\users_products_descriptions;
use App\Models\pantry_stock;

/**
 * Class for update or insert pantry stock data.
 * If model for given identifier - id or ean in given pantry id - exists it will update current pantry stock data
 * otherwise it will create new record in database
 */
class UpsertPantryStockStockServiceFromRequest extends PantryStockService
{
    public function __construct(
        private readonly UpsertPantryStockRequest $requestData
    )
    {
        parent::__construct(
            $this->requestData->user(),
            $this->requestData['pantry_id']
        );
    }

    public function __invoke()
    {
        $productStockData = $this->getPantryStockProductData();
        $this->upsertPantryStockProductData($productStockData);
        $this->upsertPantryStockProductDescription($productStockData);
        $this->upsertProductEan($productStockData);
        $productStockData->push();
        return $productStockData;
    }

    private function getPantryStockProductData(): pantry_stock
    {
        /** @var pantry_stock $pantryStockData */
        $productStockData = $this->setPantryStockQueryBuilder()->getData();
        if ($productStockData->isEmpty()) {
            if (!empty($this->requestData['pantry_stock_id'])) {
                throw new \Exception('Został podany identyfikator porduktu w spiżarni, ale nie został odnaleziony.');
            }
            $productStockData = $this->createPantryProductStockModel();
        } else {
            if ($productStockData->count() > 1) {
                throw new DuplicateRecord;
            }
            $productStockData = $productStockData[0];
        }
        return $productStockData;
    }

    private function upsertPantryStockProductData(pantry_stock $productStockData)
    {
        $productStockData->expiration_date ??= $this->requestData['expiration_date'];
        $productStockData->amount += $this->requestData['amount'];
        $productStockData->net_weight += $this->requestData['net_weight'];
        $productStockData->unit_weight = $this->requestData['unit_weight'];
        $productStockData->price = $this->requestData['price'] * 100;
    }

    private function upsertPantryStockProductDescription(pantry_stock $productStockData)
    {
        if (empty($productStockData->id)) {
            $productStockData->save();
        }
        if (empty($productStockData->description) || $this->requestData['name'] !== $productStockData->description->name) {
            $desc = $productStockData->description ?: new users_products_descriptions();
            $desc->name = $this->requestData['name'];
            $desc->users_products_stock_id = $productStockData->id;
            $desc->users_id = $this->user->id;
            $desc->img_url = $this->requestData['img_url'] ?? '';
            $desc->company = $this->requestData['company'] ?? '';
            $productStockData->setRelation('description', $desc);
        }
    }

    private function upsertProductEan(pantry_stock $productStockData)
    {
        if (($productStockData->products_ean ?? null) !== $this->requestData['ean']) {
            $product = products::firstOrCreate([
                'ean' => $this->requestData['ean']
            ]);
            $productStockData->products_id = $product->id;
            $productStockData->setRelation('products_ean', $product);
        }
    }

    private function createPantryProductStockModel(): pantry_stock
    {
        $model = new pantry_stock();
        $model->pantry_id = $this->requestData['pantry_id'];
        return $model;
    }

    protected function getUserStockId(): int
    {
        return $this->requestData['pantry_stock_id'] ?? 0;
    }

    protected function getEan(): string
    {
        return $this->requestData['ean'] ?? '';
    }
}
