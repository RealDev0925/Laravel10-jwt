<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AuthController extends Controller {

    public function __construct() {
        # By default we are using here auth:api middleware
        $this->middleware( 'auth:api', [ 'except' => [ 'login' ] ] );
    }

    public function login() {
        $credentials = request( [ 'email', 'password' ] );

        if ( ! $token = auth()->attempt( $credentials ) ) {
            return response()->json( [ 'error' => 'Unauthorized' ], 401 );
        }

        return $this->respondWithToken( $token );
        # If all credentials are correct - we are going to generate a new access token and send it back on response
    }

    public function me() {
        # Here we just get information about current user
        return response()->json( auth()->user() );
    }

    public function logout() {
        auth()->logout();
        # This is just logout function that will destroy access token of current user

        return response()->json( [ 'message' => 'Successfully logged out' ] );
    }

    public function refresh() {
        # When access token will be expired, we are going to generate a new one wit this function
        # and return it here in response
        return $this->respondWithToken( auth()->refresh() );
    }

    protected function respondWithToken( $token ) {
        # This function is used to make JSON response with new
        # access token of current user
        return response()->json( [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ] );
    }
}
