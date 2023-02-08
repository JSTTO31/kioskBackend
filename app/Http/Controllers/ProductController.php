<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\StockRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    public function index(Request $request){
        return $this->productRepository->paginate($request);
    }

    public function show(Request $request, Product $product){
        return $product->format();
    }

    public function store(ProductRequest $request){
        return $this->productRepository->create($request);
    }

    public function update(ProductRequest $request, Product $product){
        return $this->productRepository->edit($request, $product);
    }

    public function updateImage(ImageRequest $request, Product $product){
        return $this->productRepository->changeImage($request, $product);
    }

    public function destroy(Request $request, Product $product){
        $product->delete();

        return null;
    }

    public function showMostProducts(){
        return $this->productRepository->mostProduct();
    }


}
