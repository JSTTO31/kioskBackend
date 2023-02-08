<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function boot(){
        parent::boot();
        static::deleting(function($product){
            $product->stock()->delete();
            $product->sub_images()->each(function($image){
                $location = Str::replace(request()->getSchemeAndHttpHost() . '/storage/', "", $image->image) ;
                Storage::disk('public')->delete($location);

                $image->delete();
            });
            $location = Str::replace(request()->getSchemeAndHttpHost() . '/storage/', "", $product->image) ;
            Storage::disk('public')->delete($location);
        });
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-y');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-y');
    }

    public function stock(){
        return $this->hasOne(Stock::class);
    }

    public function order_items(){
        return $this->hasMany(OrderItem::class);
    }

    public function format(){
        return [
            'id' => $this->id,
            'stocks' => $this->stock->quantity,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'price' => $this->price,
            'status' => $this->status,
            'image' =>  $this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


    public function sub_images(){
        return $this->hasMany(SubImage::class);
    }
}
