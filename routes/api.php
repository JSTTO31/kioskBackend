<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSubImagesController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function() {
    // Category Below
    Route::apiResource('categories', CategoryController::class)->only(['store', 'index', 'destroy']);
    Route::apiResource('category.products', ProductController::class)->only(['store']);
    // Product Below
    Route::apiResource('products', ProductController::class)->except(['show']);
    Route::post('products/{product}/image', [ProductController::class, 'updateImage']);
    Route::get('products/show/product-most', [ProductController::class, 'showMostProducts']);
    // Stock Below
    Route::apiResource('product.stocks', StockController::class)->only(['store']);
    Route::put('product/{product}/stocks', [StockController::class, 'update']);
    // Order Below
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
    Route::get('orders/show/order-recent', [OrderController::class, 'showRecentOrders']);
    Route::put('orders/{order}/status_update', [OrderController::class, 'updateStatus']);
    // Statistics
    Route::get('statistics/orders_total', [\App\Http\Controllers\StatisticController::class, 'showNumberOfOrders']);
    Route::get('statistics/sales_total', [\App\Http\Controllers\StatisticController::class, 'showTotalSales']);
    Route::get('statistics/products_total', [\App\Http\Controllers\StatisticController::class, 'showProductTotal']);
    Route::get('statistics/profit', [\App\Http\Controllers\StatisticController::class, 'showProfit']);
    // nOtification
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);

    //Sub images controller
    Route::apiResource('product.sub-images', ProductSubImagesController::class)->only('store', 'destroy', 'update');
    Route::post('product/{product}/sub-images/{sub_image}/update', [ProductSubImagesController::class, 'updateImage']);

});
Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::apiResource('product.stocks', StockController::class)->only(['index']);
Route::get('customer/products', \App\Http\Controllers\CustomerProductController::class);
Route::apiResource('customer/categories', CustomerCategoryController::class)->only(['index']);
Route::post('orders/{order}/confirm', [OrderController::class, 'confirm']);
Route::get('orders/{order}/check', [OrderController::class, 'checkIfCompleted']);
Route::apiResource('orders', OrderController::class)->only(['store']);
Route::apiResource('order.order_items', OrderItemController::class)->only(['store']);
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('products', ProductController::class)->only(['show']);


