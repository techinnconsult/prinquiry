<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = '/home';
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function login(Request $request)
    {
        $input = $request->all();
        $credentials = [
            'email' => $input['email'],
            'password' => $input['password'],
            'confirmed' => 1
        ];
        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect($this->redirectPath());
        }else{
            flash('Please confirm your email before login.','danger');
            return redirect($this->redirectPath())->withMessage('Thanks for signing up! Please check your email.');
        }
    }

}