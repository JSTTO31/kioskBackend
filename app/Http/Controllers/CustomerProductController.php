<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $table = DB::table('products')
                ->when($request->category, function($query) use($request){
                    $query->where('category_id', $request->category);
                });
        $paginated = collect($table
                    ->leftJoin('stocks', 'stocks.product_id', '=', 'products.id')
                    ->select('products.*', 'stocks.quantity as stocks')
                    ->orderBy('products.id', 'Desc')
                    ->cursorPaginate()

        );
        $temp = $paginated["data"];
        unset($paginated["data"]);
        return response([
            'pageOptions' => $paginated,
            'products' => $temp,
        ]);
    }
}
