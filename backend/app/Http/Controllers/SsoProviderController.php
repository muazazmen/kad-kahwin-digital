<?php

namespace App\Http\Controllers;

use App\Models\SsoProvider;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SsoProviderController extends Controller
{
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleRedirect()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $ssoProvider = SsoProvider::where('provider_id', $googleUser->id)->first();

            if ($ssoProvider) {
                Auth::login($ssoProvider->user);
                return redirect('/');
            } else {
                $user = User::create([
                    'first_name' => $googleUser->user['given_name'] ?? null, 
                    'last_name' => $googleUser->user['family_name'] ?? null,
                    'email' => $googleUser->email,
                    'username' => $googleUser->nickname ?? null,
                    'avatar' => $googleUser->avatar ?? null,
                ]);

                SsoProvider::create([
                    'provider' => 'google',
                    'provider_id' => $googleUser->id,
                    'user_id' => $user->id,
                ]);

                Auth::login($user);

                return redirect('/');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }

    }
}
