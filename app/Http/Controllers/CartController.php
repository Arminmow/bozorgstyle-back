<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

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
        
        $found = false;
        foreach ($items as &$item) {
            if ($item['product_id'] == $productId) {
                $item['quantity'] += $quantity; // Increment the quantity
                $found = true;
                break;
            }
        }
        // If product isn't found, add it to the cart
        if ( !$found ) {
            $items[] = [ 'product_id' => $productId, 'quantity' => $quantity ];
        }

        // Save the updated cart
        $cart->items = $items;
        $cart->save();

        return response()->json( [ 'message' => 'Product added to cart.', 'cart' => $cart ] );
    }
}
