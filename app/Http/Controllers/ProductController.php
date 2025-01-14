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

        // Execute query and get results
        $products = $query->with('images')->get();

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
        $womenProducts = Product::where( 'gender', 'women' )->with('images')->get();
        return response()->json( $womenProducts );

    }

}
