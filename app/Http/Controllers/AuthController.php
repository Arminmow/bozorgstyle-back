<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller {
    public function register( Request $request ) {
        // Validate incoming data
        $validator = Validator::make( $request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422 );
        }

        // Create a new user
        $user = User::create( [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make( $request->password ),
        ] );

        $token = JWTAuth::fromUser( $user );

        return response()->json(compact('user','token'), 201);
    }

    public function login( Request $request ) {
        $credentials = $request->only( 'email', 'password' );

        try {
            if ( ! $token = JWTAuth::attempt( $credentials ) ) {
                return response()->json( [ 'error' => 'Invalid credentials' ], 401 );
            }

            // Get the authenticated user.
            $user = auth()->user();

            // ( optional ) Attach the role to the token.
            $token = JWTAuth::claims( [ 'role' => $user->role ] )->fromUser( $user );

            return response()->json( compact( 'token' ) );
        } catch ( JWTException $e ) {
            return response()->json( [ 'error' => 'Could not create token' ], 500 );
        }

    }

    // Get authenticated user

    public function getUser() {
        try {
            if ( ! $user = JWTAuth::parseToken()->authenticate() ) {
                return response()->json( [ 'error' => 'User not found' ], 404 );
            }
        } catch ( JWTException $e ) {
            return response()->json( [ 'error' => 'Invalid token' ], 400 );
        }

        return response()->json( compact( 'user' ) );
    }

    // User logout

    public function logout() {
        JWTAuth::invalidate( JWTAuth::getToken() );

        return response()->json( [ 'message' => 'Successfully logged out' ] );
    }
}
