<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'number';
    protected $guarded = [];

    public function order_items(){
        return $this->hasMany(OrderItem::class)
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('order_items.*', 'products.name as product_name', 'products.price as product_price',
            'products.image as product_image', DB::raw('(products.price * order_items.quantity) as total'))
            ;
    }

    public function format(){
        return [
            'id' => $this->id,
            'status' => $this->status,
            "order_items" => Product::whereHas('order_items', function($query){
                $query->where('order_id', $this->id);
            })->get(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
