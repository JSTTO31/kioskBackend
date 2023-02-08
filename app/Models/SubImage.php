<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class SubImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot(){
        parent::boot();
        static::deleting(function(SubImage $image){
            $location = Str::replace(request()->getSchemeAndHttpHost() . '/storage/', "", $image->location);
            Storage::disk('public')->delete($location);
        });
    }
}
