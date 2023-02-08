<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
//    public function index(Request $request, Product $product){
//        $stocks = Stock::whereDoesntHave('order_item')
//                  ->where('product_id', $product->id)
//                  ->get();
//
//        return $stocks;
//    }

    public function store(Request $request, Product $product){
        $request->validate(['quantity' => ['required']]);
        $stock = $product->stocks()->create(['quantity' => $request->quantity]);

        return $stock->load('product')->format();
    }

    public function update(Request $request, Product $product){
        $request->validate(['quantity' => 'required']);
        $stock = $product->stock()->update(['quantity' => $request->quantity]);

        return $stock;
    }


}
