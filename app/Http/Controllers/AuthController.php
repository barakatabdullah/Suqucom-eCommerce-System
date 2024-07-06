<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        // Get a validator for an incoming registration request.
        $validator = validator($request->only('email', 'username', 'password','avatar','user_type', "password_confirmation"), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'avatar' => 'string|max:255',
            'user_type' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $data = $request->only('email', 'username', 'password', 'avatar');
            $data['user_type'] = $request->input('user_type', 'customer');

            $user = User::create([
                'name' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'avatar' => $data['avatar'],
                'user_type' => $data['user_type'],
            ]);

            $token = $user->createToken('access')->accessToken;

            return response()->json(['data' => ['user' => $user, 'token' => $token]], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $valid = validator($request->only('email', 'password'), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            $jsonError = response()->json($valid->errors()->all(), 400);
            return response()->json($jsonError);
        }

        try {
            $credentials = $request->only(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $user = auth()->user();
            // $roles = $user->getRoleNames();
            $token = $user->createToken('access')->accessToken;
            return response()->json(['data' => ['user' => $user, 'token' => $token]], 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
