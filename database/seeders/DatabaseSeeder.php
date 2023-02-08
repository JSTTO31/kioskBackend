<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        // \App\Models\Product::all()->each(function(Product $product){
        //     $product->image = Str::replace('http:\\192.168.254.133:8000', 'http:\\\\192.168.254.133:8000', $product->image);
        //     $product->save();
        // });

         \App\Models\Category::all()->each(function(Category $category){
            $category->image = Str::replace('http://localhost:8000', 'http://192.168.254.133:8000', $category->image);
            $category->save();
        });
    }
}
