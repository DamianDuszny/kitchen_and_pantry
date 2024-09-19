<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertUserProductRequest;
use App\Models\products;
use App\Models\user;
use App\Models\users_products_descriptions;
use App\Models\users_products_stock;
use App\Services\UserProductService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return (new UserProductService($request->user()))->getUserProductStock();
    }

    public function findProductsByName(Request $request) {
        /** @var User $user */
        $user = auth('sanctum')->user();
        $query = $user->products_stock()->with(['description', 'products_ean'])
        ->whereHas('description', function (Builder $query) use ($request) {
            $query->where('name', 'LIKE', '%'.$request->get('query').'%');
        });
//        echo ( \Illuminate\Support\Str::replaceArray('?', $query->getBindings(), $query->toSql()));die;
        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpsertUserProductRequest $request)
    {
        return (new \App\Services\UpsertUserProductServiceFromRequest($request))();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $ean, Request $request)
    {
        return (new UserProductService($request->user()))->findUserProductByEan($ean);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (empty($request->post('amount'))) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'amount not specified'
                ],
                400
            );
        }
        /** @var user $currentUser */
        $user = $request->user();
        /** @var users_products_stock $productExtraData */
        $productExtraData = $user->users_products_extra_data()->find($id);
        if (empty($productExtraData)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'product with given id not found'
                ]
                , 400
            );
        }
        $productExtraData->amount = $request->post('amount');
        $productExtraData->save();
        return response()->json(['success' => true]);
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
