<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // POST /api/v1/register — autoregistro de clientes
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'cliente',
        ]);

        Customer::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'destination_city' => $request->destination_city,
        ]);

        $user->load('customer');

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // POST /api/v1/login
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = Auth::guard('api')->user()->load('customer');

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    // GET /api/v1/me
    public function me()
    {
        $user = Auth::guard('api')->user()->load('customer');

        return response()->json($user);
    }

    // POST /api/v1/logout
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}