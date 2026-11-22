<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;

class UserController extends Controller
{
    /**
     * Do the socialite login to blikk
     */
    public function login(): RedirectResponse
    {
        $socialite_user = Socialite::driver('blikk')->user();

        $user = User::updateOrCreate([
            'id' => $socialite_user->id,
        ], [
            'name' => $socialite_user->name,
            'email' => $socialite_user->email,
        ]);

        Auth::login($user);

        return redirect('/');
    }

    /**
     * Do the logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $locale = $request->session()->get('locale');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('locale', $locale);

        return redirect('/');
    }
}
