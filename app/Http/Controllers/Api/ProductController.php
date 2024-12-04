<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertPantryStockRequest;
use App\Models\user;
use App\Models\pantry_stock;
use App\Services\PantryStockService;
use App\Services\UpsertPantryStockStockServiceFromRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($pantryId, Request $request)
    {
        return (new PantryStockService($request->user(), $pantryId))->getUserProductStock();
    }

    public function findProductsByName(int $pantryId, Request $request) {
        return (new PantryStockService($request->user(), $pantryId))->findStockProductByName($request->get('name'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpsertPantryStockRequest $request)
    {
        return (new UpsertPantryStockStockServiceFromRequest($request))();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $pantryId, string $ean, Request $request)
    {
        return (new PantryStockService($request->user(), $pantryId))->findUserProductByEan($ean);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpsertPantryStockRequest $request)
    {
        return (new UpsertPantryStockStockServiceFromRequest($request))();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $ean)
    {
        /** @var User $currentUser */
        $user = auth('sanctum')->user();
        $productData = $user->products_stock()->where('products.ean', $ean)->first();
        if (empty($productData)) {
            return;
        }
        $user->products_stock()->find($productData->id)->delete();
    }
}
