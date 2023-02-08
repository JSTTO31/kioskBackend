<?php

namespace App\Repositories;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class OrderRepository
{
    public function paginated($request){
        $table = Order::with('order_items')
                ->when($request->orderId, function($query) use ($request){
                    $query->where('id', 'LIKE', "%{$request->orderId}%");
                })
                ->withCount('order_items')
                ->when($request->status, function($query) use ($request){
                    $query->where('status', $request->status);
                })
                ->when($request->orderBy, function($query) use ($request){
                    $orderBy = $request->orderBy;
                    if($orderBy == 'time'){
                        $query->orderBy('created_at', 'desc');
                    }else if($orderBy == 'total'){
                        $query->orderBy('total', 'desc');
                    }else if($orderBy == 'id'){
                        $query->orderBy('id', 'desc');
                    }else if($orderBy == 'item'){
                        $query->orderByRaw('order_items_count desc');
                    }else if($orderBy == 'status'){
                        $query->orderBy(DB::raw("status = 'pending', status = 'completed', status = 'cancelled'"));
                    }
                })
                ->when($request->status, function($query) use ($request){
                    $query->where('status', $request->status);
                })
                ->latest();

        $paginated = collect($table->paginate(12));
        $temp = $paginated["data"];
        unset($paginated["data"]);
        return [
            'pageOptions' => $paginated,
            'orders' => $temp
        ];
    }

    public function format($order){
        return $order->load('order_items');
    }

    public function create($request){
        $orderNumber = Str::upper(Str::random(1)) . rand(100, 999);
        $order = Order::create([...$request->only(['id', 'total', 'location']), 'order_number' => $orderNumber, 'status' => 'pending']);
        return $order;
    }

    public function editStatus($request, $order){
        $order->status = $request->status;
        $order->save();
        return $order;
    }

    public function recentOrders(){
        return Order::with('order_items')->withCount('order_items')->latest()->limit(3)->get();
    }

}
