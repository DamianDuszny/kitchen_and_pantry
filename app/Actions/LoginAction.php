<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginAction {
    public function ActionRun(Request $request): string {
        $user = User::where([
            'email_address' => $request->email_address,
        ])->first();

        if(empty($user->email_address)) {
            throw new \Exception('Email address not found in database');
        }

        if(!Hash::check($request->password, $user->password)) {
            throw new \Exception( 'Password is invalid');
        }

        return $user->createToken('Api token for ' . $user->email_address)->plainTextToken;
    }
}
