<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function getAll()
    {
        $users = User::with('roles')->get();

        return response()->json(['data' => $users], 200);
    }

    public function getOne($id)
    {
        $user = User::with('roles')->find($id);

        return response()->json(['data' => $user], 200);
    }

}
