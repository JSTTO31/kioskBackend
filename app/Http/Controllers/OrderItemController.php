<?php

namespace App\Http\Controllers;

use App\Event\OrderCompleted;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function store(Request $request, Order $order){
        $request->validate(['product_id' => ['required'], 'quantity' => ['required']]);
         $lastItem = $request->lastItem ?? false;
         $orderItem = $order->order_items()->create($request->only(['quantity', 'product_id']));
        if($lastItem){
            broadcast(new OrderCompleted($order));
        }
        return response(null, 204);
    }
}
