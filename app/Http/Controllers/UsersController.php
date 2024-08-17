<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function getAll()
    {
        $users = User::with('roles')->get();
        $users->each(fn($user) => $user->avatar = $user->getAvatarUrlAttribute());
        return response()->json(['data' => $users], 200);
    }

    public function getOne($id)
    {
        $user = User::with(['roles', 'permissions'])->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['data' => $user], 200);
    }

    public function create(Request $request)
    {
        $validator = validator($request->only('email', 'fname','lname', 'password','role','avatar','permissions'), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required|integer|exists:roles,id',
            'permissions' => 'array',
            ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $data = $request->only('email', 'fname','lname', 'password', 'avatar', 'role', 'permissions');

            $imageName = time().'.'.$request->avatar->extension();
            $imagePath = $request->file('avatar')->storeAs('images', $imageName, 'public');

            $user = User::create([
                'name'=> $data['fname'] . ' ' . $data['lname'],
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'avatar' => $imagePath ?? null,
            ]);

            $role = Role::findOrFail($data['role']);
            $user->assignRole($role);


            if(isset($data['permissions'])) {
                $user->givePermissionTo($data['permissions']);
            }

            return response()->json(['data' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


  }


}
