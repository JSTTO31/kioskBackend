<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request){
        return $this->orderRepository->paginated($request);
    }

    public function store(OrderRequest $request){

        return $this->orderRepository->create($request);
    }

    public function updateStatus(Request $request, Order $order){
        $request->validate(['status' => ['required', 'in:pending,completed,cancelled']]);
        return $this->orderRepository->editStatus($request, $order);
    }

    public function show(Request $request, Order $order){
        return $this->orderRepository->format($order);
    }

    public function showRecentOrders(Request $request){
        return $this->orderRepository->recentOrders();
    }

    public function confirm (Order $order){
        $order->status = "completed";
        $order->save();
        return $order;
    }

    public function checkIfCompleted(Order $order){

        return $order->status == 'completed';
    }
}

