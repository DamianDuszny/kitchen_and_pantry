<?php

namespace App\Http\Controllers;

use App\Actions\LoginAction;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    use ValidatesRequests;
    //@todo logout method
    public function register(RegisterRequest $request) {
        $request->validated($request->all());

        $user = (new User())->SetFromRequest($request);
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

    public function update(Request $request) {
        return response()->json(
            [
                'id' => auth('sanctum')->user()->getAuthIdentifier()
            ]
        );
    }

    public function userData() {

    }
}
