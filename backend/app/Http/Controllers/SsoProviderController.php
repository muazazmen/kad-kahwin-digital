<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\SsoProvider;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
                $token = $ssoProvider->user->createToken($ssoProvider->user->email)->plainTextToken;
                return redirect()->away(env('FRONTEND_URL') . "?accessToken=" . $token);
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

                Mail::to($user->email)->send(new WelcomeMail($user));

                // Generate API Token
                $token = $user->createToken($user->email)->plainTextToken;

                Auth::login($user);

                // Redirect to Vue.js Dashboard with token in URL
                return redirect()->away(env('FRONTEND_URL') . "?accessToken=" . $token);
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
