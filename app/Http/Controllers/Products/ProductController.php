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
        return products::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        echo 2;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var user $currentUser */
        $currentUser = $request->user();
        /** @var products $product */
        $product = products::where('ean', $request->post('ean'))->firstOr(function () use($request) {
            $product = new products();
            $product->ean = $request->post('ean');
            $product->type = $request->post('type') ?? 1;
            $product->save();
            return $product;
        });

        /** @var users_products_extra_data $productExtraData */
        $productExtraData = $currentUser->users_products_extra_data()->find($product->id);
        if(empty($productExtraData)) {
            $productExtraData = new users_products_extra_data();
            $productExtraData->products_id = $product->id;
            $productExtraData->users_id = $currentUser->id;
        }
        $productExtraData->price = $request->post('price') * 100 ?? 0;
        $productExtraData->name = $request->post('name') ?? '';
        $productExtraData->unit_weight = $request->post('unit_weight') ?? 0;
        $productExtraData->net_weight = $request->post('net_weight') ?? 0;
        $productExtraData->amount = $request->post('amount') ?? 0;
        $productExtraData->save();

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        echo $id;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
