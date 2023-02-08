<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductSubImageRequest;
use App\Models\Product;
use App\Models\SubImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductSubImagesController extends Controller
{
    public function store(ProductSubImageRequest $request, Product $product){
        $location = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('image')->store('images/sub-product', 'public');
        $subImages = $product->sub_images()->create([
            'location' => $location,
        ]);
        return $subImages;
    }

    public function updateImage(ProductSubImageRequest $request, Product $product, SubImage $subImage){
        $location = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('image')->store('images/sub-product', 'public');
        $locationToDelete = Str::replace($request->getSchemeAndHttpHost() . '/storage/', '', $subImage->location);
        Storage::disk('public')->delete($locationToDelete);
        $subImage->location = $location;
        $subImage->save();

        return $location;
    }

    public function destroy(Request $request, Product $product, $subImage){
        collect(explode(',', $subImage))->each(function($id){
            $subImage = SubImage::find($id);
            $location = str_replace(request()->getSchemeAndHttpHost() . '/storage/', '', $subImage->location);
            Storage::disk('public')->delete($location);
        });

        SubImage::whereIn('id', explode(',', $subImage))->delete();

        return null;
    }
}
