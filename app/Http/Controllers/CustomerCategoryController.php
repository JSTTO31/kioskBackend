<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CustomerCategoryController extends Controller
{
    public function index(){
        return Category::all();
    }
}
