<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        return Category::all();
    }

    public function store(CategoryRequest $request){
        $location = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('image')->store('images', 'public');
        $category = Category::create([
            'name' => $request->name,
            'image' => $location,
        ]);

        return $category->load('products');
    }

    public function destroy(Request $request, Category $category){
        $category->delete();

        return response(null);
    }
}
