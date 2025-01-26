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

        // Get the user's cart
    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {
        return response()->json(['error' => 'Cart not found'], 404);
    }

    // Check if the product is in the cart
    $found = false;
    $items = $cart->items ?? [];

    foreach ($items as &$item) {
        if ($item['product_id'] == $productId) {
            $found = true;

            if ($item['quantity'] > 1) {
                // If quantity is greater than 1, decrease it by 1
                $item['quantity'] -= 1;
            } else {
                // If quantity is 1, remove the item from the cart
                $items = array_filter($items, fn($i) => $i['product_id'] !== $productId);
            }
            break;
        }
    }

    if (!$found) {
        return response()->json(['error' => 'Product not found in cart'], 404);
    }

    // Save the updated cart
    $cart->items = array_values($items); // Re-index the array after removing item
    $cart->save();

    return response()->json(['message' => 'Product removed from cart or quantity decreased', 'cart' => $cart ] );
    }

}
