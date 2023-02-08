<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StatisticRepository
{
    public function totalSales(){
        return Order::withCount('order_items')
            ->withSum('order_items', 'quantity')
            ->where('status', 'completed')
            ->get()
            ->sum(DB::raw('order_items_sum_quantity'));
    }

    public function numberOfOrders(){
        return Order::all()->count();
    }

    public function numberOfProducts(){
        return Product::all()->count();
    }

    public function profit(){
        return OrderItem::whereHas('order', function($query){
                $query->where('status', 'completed');
            })
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(DB::raw('products.price * order_items.quantity as total'))
            ->get()
            ->sum('total');
    }
}
