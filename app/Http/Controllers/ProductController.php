<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;


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
        if ( $request->has( 'gender' ) ) {
            $query->where( 'gender', $request->gender );
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
        // Validate the input data
        $validator = Validator::make( $request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'gender' => 'required|in:men,women',
            'category_id' => 'required|integer|exists:categories,id', // Assuming you have a categories table
        ] );

        // Return validation errors if any
        if ( $validator->fails() ) {
            return response()->json( [
                'success' => false,
                'errors' => $validator->errors(),
            ], 400 );
        }

        try {
            // Create a new product in the database
            $product = Product::create( [
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'gender' => $request->gender,
                'category_id' => $request->category_id,
            ] );

            return response()->json( [
                'success' => true,
                'product' => $product,
            ], 201 );

        } catch ( \Exception $e ) {
            // Handle any errors and return failure response
            return response()->json( [
                'success' => false,
                'message' => 'Error creating product',
            ], 500 );
        }
    }

}
