<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if ( auth()->user()->hasPermissionTo('can_access_web')) {
            return redirect()->intended($this->redirectPath());
        }
        
        Auth::logout();
        return redirect()->route('login')->withErrors(['role' => 'You are not authorized.']);
    }
}

