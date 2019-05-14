<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = new User();
        $user->email = $request->get('email');
        $user->password = $request->get('password');
        $user->save();

        $token = auth()->login($user);
        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth()->attempt($credentials)) {

            return response([
                'error' => 'Unauthorized',
                401
            ]);
        }
        return $this->respondWithToken($token);
    }
    
    public function logout()
    {
        auth()->logout();
        return response([
            'message' => 'Successfully logged out!'
        ]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
