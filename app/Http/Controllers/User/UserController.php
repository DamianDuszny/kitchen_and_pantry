<?php

namespace App\Http\Controllers\User;

use App\Actions\LoginAction;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    use ValidatesRequests;
    //@todo logout method
    public function register(RegisterRequest $request) {
        $request->validated($request->all());

        $user = (new User())->setFromRequest($request);
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
            abort(401, $e->getMessage());
        }

        return response()->json(['token' => $token]);
    }

    public function update(UserUpdateRequest $request) {
        $request->validated($request->all());
        /** @var User $user */
        $user = $request->user();
        $user->setFromRequest($request);
        $user->save();
    }
}
