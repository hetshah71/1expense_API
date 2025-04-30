<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use \Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
//use Illuminate\Support\Facades\Log;
use App\Http\Requests\RegisterRequest;
class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $credentials = $request->validated();

            $user = User::create($credentials);
            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success(['token' => $token, 'user'=>$user], "User registered successfully");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Validation error', $e->errors(), 422);
        } catch (\Exception $e) {
            return ApiResponse::error('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials)) {
                return ApiResponse::error('Invalid credentials', [], 401);
            }

            else{
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;
                return ApiResponse::success(['token' => $token,'user'=> $user], "Logged in successfully");
            } 
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Validation error', $e->errors(), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::error('User not found', [], 404);
        } catch (\Exception $e) {
            // \Log::error('Error during login: ' . $e->getMessage());
            return ApiResponse::error('Login failed: ' . $e->getMessage(), [], 500);
        }
    }
    public function logout()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            $user->tokens()->delete();
            return ApiResponse::success([], "Logged out successfully");
        } catch (\Exception $e) {
            // \Log::error('Error during logout: ' . $e->getMessage());
            return  ApiResponse::error('Logout failed: ' . $e->getMessage(), [], 500);
        }
    }
}
