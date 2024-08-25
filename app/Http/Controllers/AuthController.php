<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        $validator = validator($request->only('email', 'fname', 'lname', 'phone', 'city', 'password', "password_confirmation", 'avatar'), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|numeric|max:999999999',
            'city' => 'required|string|max:255',
            'avatar' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $data = $request->only('email', 'fname', 'lname', 'password', 'avatar');

            $user = User::create([
                'name' => $data['fname'] . ' ' . $data['lname'],
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'avatar' => $data['avatar'] ?? null,
            ]);

            try {
                $role = Role::where('name', 'Customer')->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Role not found.'], 404);
            }

            $user->assignRole($role);

            $user->contactDetails()->create([
                'phone' => $request->phone,
                'city' => $request->city,
            ]);

            $token = $user->createToken('access')->accessToken;

            return response()->json(['data' => ['user' => $user, 'token' => $token]], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $valid = validator($request->only('email', 'password'), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors()->all(), 400);
        }

        try {
            $credentials = $request->only(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = auth()->user();
            $role = $user->getRoleNames();
            $token = $user->createToken('access')->accessToken;

            return response()->json(['data' => ['user' => $user, 'token' => $token]], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

}
