<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    //
    public function login()
    {
        //return Socialite::driver('github')->stateless()->redirect();


        return Socialite::with('github')->stateless()->redirect()->getTargetUrl();
    }

    public function redirect()
    {
        $githubUser = Socialite::with('github')->stateless()->user();


        // Create a new user in your database

        $user = User::updateOrCreate([
            'provider_id' => $githubUser->id,
        ], [
            'name' => $githubUser->name ?? $githubUser->login ,
            'email' => $githubUser->email,
            'password' => bcrypt('randompassword'),
        ]);
        // Log in the new user
        auth()->login($user, true);

        // Generate a personal access token
        $userToken = $user->token() ?? $user->createToken('socialLogin');

        return [
            "token_type" => "Bearer",
            "access_token" => $userToken->accessToken
        ];

    }
}

