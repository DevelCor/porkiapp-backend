<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('farms');

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => $user
            ]
        ], 200);
    }
}
