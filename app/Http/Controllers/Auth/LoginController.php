<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Http\Controllers\Controller;
use Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleProviderCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        // Vérifier si l'utilisateur a déjà un compte
        $user = User::where('provider_id', $githubUser->getId())->first();

        // Au cas où l'utilisateur n'a pas de compte, nnous créons un utilisateur
        if (!$user) {
            $user = User::create([
                'provider_id' =>$githubUser->getId(),
                'email' => $githubUser->getEmail(),
                'name' => $githubUser->getName(),
            ]);
        }

        // Authentification de l'utilisateur
        Auth::login($user, true);

        return redirect($this->redirectTo);
    }
}
