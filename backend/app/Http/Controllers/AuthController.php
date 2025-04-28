<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use \Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($credentials);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
           'token' => $token,
           'message' => 'Registration successful'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' =>'required|string|email',
            'password' =>'required|string',
        ]); 

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'type' => 'Error',
                'message' => 'Invalid credentials'
            ], 401);
        } 
        else {
            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'type' => 'Success',
                'user' => $user,
               'token' => $token,
              'message' => 'Login successful'
            ]);
        }
    }
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}
