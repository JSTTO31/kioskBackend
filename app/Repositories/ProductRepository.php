<?php

namespace App\Repositories;

use App\Events\ProductUpdate;
use App\Models\OrderItem;
use App\Models\Product;
use http\Client\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductRepository
{
    public function paginate($request){
        $stocks_max = $request['stocks']['max'] ?? false;
        $stocks_min = $request['stocks']['min'] ?? false;
        $sales_max = $request['sales']['max'] ?? false;
        $sales_min = $request['sales']['min'] ?? false;

        $table = Product::withCount(['order_items as sales' => function($query){
                        $query->select(DB::raw('order_items.quantity * products.price'));
                    }, 'stock as stocks' => function($query){
                        $query->select('stocks.quantity');
                    }])
                    ->with(['sub_images'])
                    ->when($request->status, function ($query) use ($request){
                        $query->where('status', $request->status);
                    })
                    ->when($request->category, function($query) use ($request){
                        $query->where("category_id", $request->category);
                    })
                    ->where('name', 'LIKE', "%{$request->name}%")
                    ->when($stocks_min, function($query) use ($request){
                        $query->groupBy('id')
                            ->havingRaw("stocks >= {$request['stocks']['min']}");

                    })
                    ->when($stocks_max, function($query) use ($request){
                        $query->groupBy('id')
                            ->havingRaw("stocks <= {$request['stocks']['max']}");

                    })
                    ->when($sales_min, function($query) use ($request){
                        $query->groupBy('id')
                            ->havingRaw("sales >= {$request['sales']['min']}");

                    })
                    ->when($sales_max, function($query) use ($request){
                        $query->groupBy('id')
                            ->havingRaw("sales <= {$request['sales']['max']}");

                    })
                    ->when(!$request->orderBy, function($query){
                        $query->orderBy('id', 'desc');
                    })
                    ->when($request->orderBy, function ($query) use ($request){
                        if($request->orderBy == 'date'){
                            $query->orderByRaw("created_at desc");
                        }else if($request->orderBy == 'status'){
                            $query->orderByRaw("status = 'pending', status = 'disable', status = 'available' desc");
                        }else if($request->orderBy == 'stocks'){
                            $query->orderByRaw("stocks desc");
                        }else if($request->orderBy == 'sales'){
                            $query->orderByRaw("sales desc");
                        }else if($request->orderBy == 'price'){
                            $query->orderByRaw("price desc");
                        }else{
                            $query->orderByRaw("id desc");
                        }
                    });
        $paginated = collect($table->paginate(12));
        $data = $paginated["data"];
        unset($paginated["data"]);
        $groupByStatus = DB::table('products')->selectRaw('COUNT(*) as count, status')->groupBy('status')->get();
        $totalStacks = DB::table('stocks')->selectRaw('SUM(quantity) as totalStocks')->first()->totalStocks;
        $totalSales = DB::table('orders')->where('status', 'completed')->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')->selectRaw('SUM(order_items.quantity) as sum')->first()->sum;
        // $overallStocks = DB::table('products')->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')->selectRaw('SUM(stocks.quantity + ?) as overall', [$data['']])->get();
        return [
            'products' => $data,
            'pageOptions' => $paginated,
            'total_available' => $groupByStatus->where('status', 'available')->first()->count ?? 0,
            'total_disabled' => $groupByStatus->where('status', 'disable')->first()->count ?? 0,
            'total_draft' => $groupByStatus->where('status', 'draft')->first()->count ?? 0,
            'total_stocks' => $totalStacks,
            'total_sales' => $totalSales,
        ];
    }

    public function edit($request, $product){
        $product->update($request->only(['name', 'price', 'category_id', 'status']));
        $product->stocks = $request->stocks;
        $product->stock()->update(['quantity' => $product->stocks]);

        return [
            'id' => $product->id,
            'stocks' => $product->stocks,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'status' => $product->status,
            'price' => $product->price,
            'image' =>  $product->image,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    }

    public function create($request){
        $location = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('image')->store('images/product/', 'public');
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $location,
            'category_id' => $request->category_id,
            'status' => 'draft',
        ]);
        $product->stock()->create(['quantity' => $request->stocks]);

        return $product->format();
    }

    public function changeImage($request, $product){
        $location = Str::replace($request->getSchemeAndHttpHost() . '/storage/', '', $product->image);
        $newLocation = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('image')->store( 'images/product/', 'public');
        Storage::disk('public')->delete($location);
        $product->image = $newLocation;
        $product->save();
        return $newLocation;
    }

    public function mostProduct(){
        return Product::with(['order_items' => function($query){
            $query->whereHas('order', function ($query){
                $query->where('status', 'completed');
            });
        }])
        ->withCount('order_items')
        ->withSum('order_items', 'quantity')
        ->orderByRaw('order_items_sum_quantity * price desc')
        ->limit(6)
        ->get();
    }


}
