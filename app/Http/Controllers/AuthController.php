<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // $credentials = $request->only(['email', 'password']);

        // if (!$token = auth()->attempt($credentials)) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        // return response()->json(['token' => $token]);

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json(['message' => 'Login credentials are invalid'], 401);
        }

        $user = User::where('email', $validated['email'])->firstOrFail();

        // return response()->json([
        //     'token' => $user->createToken('auth_token')->plainTextToken,
        //     'token_type' => 'Bearer',
        // ]);
        return response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255|string',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // return response()->json([
        //     'token' => $user->createToken('auth_token')->plainTextToken,
        //     'token_type' => 'Bearer',
        // ]);
        return response()->json([
            'data' => $user,
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }
}
