<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id', // Validate product exists
            'image_path' => 'required|url', // Validate the image URL or path
        ]);

        // Create a new record in the product_images table
        $productImage = ProductImage::create([
            'product_id' => $request->input('product_id'),
            'image_path' => $request->input('image_path'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image added successfully',
            'data' => $productImage,
        ], 201);
    }
}
