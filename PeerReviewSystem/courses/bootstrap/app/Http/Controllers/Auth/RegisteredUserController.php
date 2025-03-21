<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            's_number' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^S\d+$/'], // Validation to ensure s_number starts with 'S'
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create a new user with role set to 'student'
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        's_number' => $request->s_number, // Insert s_number
        'password' => Hash::make($request->password),
        'role' => 'student', // Automatically set role to 'student'
    ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
