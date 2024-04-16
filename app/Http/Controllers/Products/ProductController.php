<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\user;
use App\Models\users_products_extra_data;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = auth('sanctum')->user();
        return $user->products()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var user $currentUser */
        $user = $request->user();
        /** @var products $product */
        $product = products::where('ean', $request->post('ean'))->firstOr(function () use ($request) {
            $product = new products();
            $product->ean = $request->post('ean');
            $product->save();
            return $product;
        });

        /** @var users_products_extra_data $productExtraData */
        $productExtraData = $user->users_products_extra_data()->find($product->id);
        if (empty($productExtraData)) {
            $productExtraData = new users_products_extra_data();
            $productExtraData->products_id = $product->id;
            $productExtraData->users_id = $user->id;
        }
        $productExtraData->price = (($request->post('price') ?? 0) * 100) ?: $productExtraData->price;
        $productExtraData->name = $request->post('name') ?? $productExtraData->name;
        $productExtraData->unit_weight = $request->post('unit_weight') ?? $productExtraData->unit_weight;
        $productExtraData->net_weight += $request->post('net_weight') ?? 0;
        $productExtraData->amount += $request->post('amount') ?? 0;
        $productExtraData->save();

    }

    /**
     * Display the specified resource.
     */
    public function show(string $ean)
    {
        /** @var User $user */
        $user = auth('sanctum')->user();
        return $user->products()->where('products.ean', $ean)->get();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $ean)
    {
        /** @var User $currentUser */
        $user = auth('sanctum')->user();
        $productData = $user->products()->where('products.ean', $ean)->first();
        if(empty($productData)) {
            return;
        }
        $user->users_products_extra_data()->delete($productData->id);
    }
}
