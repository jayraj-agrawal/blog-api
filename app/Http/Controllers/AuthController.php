<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'type' => 'error',
                'msg' => 'The provided credentials do not match our records'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('MyAuthApp')->plainTextToken;

        return response()->json([
            'type' => 'success',
            'msg' => 'Login successfully',
            'data' => $token
        ], 200);
    }
}
