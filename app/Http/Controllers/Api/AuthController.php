<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\LoginUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends ApiController
{

    /**
     * Login
     *
     * Authenticates the user and returns the user's API token.
     *
     * @unauthenticated
     * @group Authentication
     * @response 200 {
    "data": {
    "token": "{YOUR_AUTH_KEY}"
    },
    "message": "Authenticated",
    "status": 200
    }
     */
    public function login(LoginUserRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'API Token for ' . $request->email,
                    Abilities::getAbilities($user),
                    now()->addMonth()
                )->plainTextToken
            ]
        );
    }


    /**
     * Logout
     *
     * Signs out the user and destroy's the API token.
     *
     * @group Authentication
     * @response 200 {}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}
