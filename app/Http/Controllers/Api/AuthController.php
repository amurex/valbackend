<?php

namespace App\Http\Controllers\Api;

use App\Events\UserloginHistory;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (auth()->attempt($request->all())) {
            $user = auth()->user();
            $user['ip'] = $request->getClientIp();
            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            Event::dispatch(new UserloginHistory($user));

            return response([
                'status' => "OK",
                'message' => "Logged in",
                'user' => $user,
                'access_token' => $accessToken,
            ], Response::HTTP_OK);
        }

        return response([
            'message' => 'This User does not exist'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function register(Request $request)
    {
        $request['type'] = $request['type'] ? $request['type'] : 0;
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(10),
            'type' => $request->type,
        ]);

        $token = $user->createToken('authToken')->accessToken;
        $response = ['token' => $token];
        return response($response, Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $user = auth('api')->user();

        if ($user) {
            $tokenRepository->revokeAccessToken($user->token()->id);
            $user->token()->revoke();
            $user->token()->delete();
        }

        return response([
            'message' => 'Successfully logged out'
        ], Response::HTTP_OK);
    }
}
