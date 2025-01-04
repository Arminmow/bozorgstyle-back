<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all(); // Fetch all products from the database
        return response()->json($products); // Return the data as JSON
    }
}
