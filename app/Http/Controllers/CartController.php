<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller {

    public function view( Request $request ) {
        // Retrieve the authenticated user
        $user = $request->user();

        // Fetch cart items for the user
        $cartItems = Cart::where( 'user_id', $user->id )
        ->with( 'product' ) // Assuming a relationship to get product details
        ->get();

        // Return the cart items as JSON response
        return response()->json( [
            'success' => true,
            'cart' => $cartItems,
        ] );
    }

    public function addToCart( Request $request ) {
        $user = $request->user();
        // Get the authenticated user
        $productId = $request->input( 'product_id' );
        $quantity = $request->input( 'quantity', 1 );
        // Default to 1 if not provided

        // Check if the product exists
        $product = Product::find( $productId );
        if ( !$product ) {
            return response()->json( [ 'error' => 'Product not found.' ], 404 );
        }

        // Get or create the user's cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Update the items in the cart
        $items = $cart->items ?? [];
        if (isset($items[$productId])) {
            $items[$productId] += $quantity; // Increment quantity
        } else {
            $items[$productId] = $quantity; // Add new product
        }
        $cart->items = $items;
        $cart->save();

        return response()->json(['message' => 'Product added to cart.', 'cart' => $cart ] );
    }
}
