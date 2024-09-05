<?php

namespace App\Http\Controllers\Api;

use App\Actions\LoginAction;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\user;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    use ValidatesRequests;
    //@todo logout method
    public function register(RegisterRequest $request) {
        $request->validated($request->all());

        $user = (new user())->setFromRequest($request);
        $user->save();
        return response()->json(
            [
                'success' => true,
                'token' => $user->createToken('Api token for ' . $user->email_address)->plainTextToken
            ]
        );
    }

    public function login(Request $request, LoginAction $loginAction) {
        try {
            $token = $loginAction->ActionRun($request);
        } catch(\Exception $e) {
            return response(['message' => $e->getMessage()], 401);
        }

        return response()->json(['token' => $token]);
    }

    public function update(UserUpdateRequest $request) {
        $request->validated($request->all());
        /** @var user $user */
        $user = $request->user();
        $user->setFromRequest($request);
        $user->save();
    }

    public function sendPasswordResetLink(Request $request) {
        $request->validate(['email_address' => 'required|email']);

        $status = Password::sendResetLink(['email'=>$request->only('email_address')]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 400);
    }

    public function changePasswordWithToken(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
