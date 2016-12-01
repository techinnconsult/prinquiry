<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }
    /*
     * register: Override Function to register and add confirmation code and send email.
     */
    public function register(Request $request)
    {
        $rules = [
            'username' => 'required|min:6|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6'
        ];

        $input = $request->all();

        $this->validator($request->all())->validate();

        $confirmation_code = str_random(30);
        
        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'company_name' => $input['company_name'],
            'mobile_phone' => $input['mobile_phone'],
            'password' => bcrypt($input['password']),
            'confirmation_code' => $confirmation_code
        ]);
        $code['confirmation_code'] = $confirmation_code;
        
        Mail::send('emails.verify', $code, function($message) {
            $message->to($_REQUEST['email'], $_REQUEST['name'])
                ->subject('Verify your email address');
        });
        
        return redirect($this->redirectPath())->withMessage('Thanks for signing up! Please check your email.');
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'company_name' => $data['company_name'],
            'mobile_phone' => $data['mobile_phone'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
    public function verify($confirmation_code){
        
    }
}