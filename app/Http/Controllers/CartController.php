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
        $cart = Cart::where( 'user_id', $user->id )
        ->with( 'product' ) // Assuming a relationship to get product details
        ->first();
        // Use first() to get a single cart

        // Return the cart as JSON response
        if ( $cart ) {
            return response()->json( [
                'success' => true,
                'cart' => $cart, // Return a single cart object
            ] );
        }

        return response()->json( [
            'success' => false,
            'message' => 'Cart not found',
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

    public function removeFromCart( Request $request ) {
        $user = $request->user();
        $productId = $request->input( 'product_id' );
        // No need for quantity input, it's always 1

        // Get the user's cart
        $cart = Cart::where( 'user_id', $user->id )->first();
        if ( !$cart ) {
            return response()->json( [ 'error' => 'Cart not found.' ], 404 );
        }

        // Get the current items in the cart
        $items = $cart->items ?? [];
        $updatedItems = [];
        $itemFound = false;

        foreach ( $items as $item ) {
            if ( $item[ 'product_id' ] == $productId ) {
                // Decrease the quantity by 1
                $item[ 'quantity' ] -= 1;

                // If quantity goes to 0 or below, remove the item
                if ( $item[ 'quantity' ] <= 0 ) {
                    continue;
                }
                $itemFound = true;
            }

            $updatedItems[] = $item;
            // Keep the remaining items
        }

        // If the item was found and updated, save the cart
        if ( $itemFound ) {
            $cart->items = $updatedItems;
            $cart->save();
            return response()->json( [ 'message' => 'Item quantity updated.', 'cart' => $cart ] );
        }

        return response()->json( [ 'error' => 'Item not found in cart.' ], 404 );
    }
}
