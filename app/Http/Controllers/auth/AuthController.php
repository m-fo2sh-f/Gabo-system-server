<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{


   public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'بيانات خاطئة'], 401);
    }

    $user = Auth::user();
    
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'user' => $user,
        'token_type' => 'Bearer',
        'expires_in_days' => 3 // مجرد معلومة للفرونت إند
    ]);
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
}

public function user(Request $request)
{
    $user = $request->user();
    return response()->json([
        'access_token' => $user->createToken('auth_token')->plainTextToken,
        'user' => $user,
        'token_type' => 'Bearer',
        'expires_in_days' => 3 // مجرد معلومة للفرونت إند
    ]);
}
    
}
