<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function email(Request $request)
    {
        $request->validate([
            'email' => [
                'required', 'unique:users,email', 'email', 'max:255'
            ],

            'password' => [
                'required'
            ],

            'password_repeat' => [
                'required', 'same:password'
            ],

            'agreed_terms' => [
                'required', 'accepted'
            ]
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return ['success' => true];
    }

    public function facebook(Request $request)
    {

    }

    public function google(Request $request)
    {

    }
}
