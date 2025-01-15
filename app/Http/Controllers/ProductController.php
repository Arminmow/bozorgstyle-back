<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller {
    public function index( Request $request ) {
        $query = Product::query();

        // Filter by name ( if provided )
        if ( $request->has( 'name' ) ) {
            $query->where( 'name', 'like', '%' . $request->input( 'name' ) . '%' );
        }

        // Filter by price range ( if provided )
        if ( $request->has( 'min_price' ) ) {
            $query->where( 'price', '>=', $request->input( 'min_price' ) );
        }
        if ( $request->has( 'max_price' ) ) {
            $query->where( 'price', '<=', $request->input( 'max_price' ) );
        }
        if ( $request->has( 'category_id' ) ) {
            $query->where( 'category_id', $request->category_id );
        }
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        // Execute query and get results
        $products = $query->with( 'images' )->get();

        return response()->json( $products );
    }

    public function show( $id ) {
        $product = Product::with( 'images' )->findOrFail( $id );
        return response()->json( $product );
    }

    // Get all men's products
     public function getMenProducts()
     {
         $menProducts = Product::where('gender', 'men')->with('images')->get();
         return response()->json($menProducts);  
     }

     // Get all women's products

    public function getWomenProducts() {
        $womenProducts = Product::where( 'gender', 'women' )->with( 'images' )->get();
        return response()->json( $womenProducts );

    }

    public function store( Request $request ) {
        $validated = $request->validate( [
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ] );

        $product = Product::create( $validated );
        return response()->json( $product, 201 );
    }

}
