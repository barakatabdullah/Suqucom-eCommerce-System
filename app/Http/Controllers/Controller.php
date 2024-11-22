<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Settings\AppSettings;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected function ApiResponseFormatted($code = 401, $data = NULL, $message = "Unauthenticated", Request $request = null): JsonResponse
    {
        $headers =[];

        if($request != null && $request->user() != null){
            /* @var $user User*/
            $user = $request->user();
            $headers["phone_verified_at"] = $user->phone_verified_at;
            $headers["email_verified_at"] = $user->email_verified_at;
        }

        if($data == null){
            $data = $message;
        }

        return response()->json($data)->setStatusCode($code, $message)->withHeaders($headers);
    }

}
