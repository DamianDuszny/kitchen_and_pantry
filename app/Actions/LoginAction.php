<?php

namespace App\Actions;

use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginAction {
    public function ActionRun(Request $request): string {
        $user = user::where([
            'email' => $request->post('email_address'),
        ])->first();

        if(empty($user->email)) {
            throw new \Exception('Email address not found in database');
        }

        if(!Hash::check($request->post('password'), $user->password)) {
            throw new \Exception( 'Password is invalid');
        }

        return $user->createToken('Api token for ' . $user->email_address)->plainTextToken;
    }
}
