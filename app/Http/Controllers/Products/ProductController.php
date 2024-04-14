<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\users_products;
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
        //1234567
        //@todo transaction
        //@todo walidacja danych
        /** @var products $product */
        $product = products::where('ean', $request->post('ean'))->firstOr(function () use($request) {
            $product = new products();
            $product->ean = $request->post('ean');
            $product->type = $request->post('type') ?? 1;
            $product->save();
            return $product;
        });
        $usersProducts = new users_products(
            [
                'users_id'=> $request->user()->id,
                'products_id' => $product->id
            ]
        );
        $request->user()->products->save($usersProducts); //ewentualnie na odwrót
//        $usersProducts = new users_products();
//        $usersProducts->users_id = $request->user()->id;
//        $usersProducts->products_id = $product->id;
//        $usersProducts->save();
        $usersProductsExtraData = new users_products_extra_data();
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
