<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {

            $user = Socialite::driver('facebook')->user();

            $findUser = User::where('facebook_id', $user->id)->first();

            if ($findUser) {

                Auth::login($findUser);

                return redirect()->intended('home');
            } else {
                $newUser = User::updateOrCreate(['email' => $user->email], [
                    'name' => $user->name,
                    'facebook_id' => $user->id,
                    'password' => encrypt('123456dummy')
                ]);

                Auth::login($newUser);

                return redirect()->intended('home');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
