<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function getAll()
    {
        $users = User::all();

        return response()->json(['data' => $users], 200);
    }
}
