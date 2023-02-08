<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function format(){
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'products' => $this->products->map->format(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function customerFormat(){
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' =>  $this->image,
            'products' => collect($this->products)->map->format() ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function products(){
        return $this->hasMany(Product::class);
    }
}
