<?php

namespace Acelle\Http\Controllers\Auth;

use Acelle\Http\Controllers\Controller;
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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $rules = [
            $this->username() => 'required',
            'password' => 'required'
        ];
        
        if (\Acelle\Model\Setting::get('login_recaptcha') == 'yes') {
            if(!\Acelle\Library\Tool::checkReCaptcha($request)) {
                $rules['recaptcha_invalid'] = 'required';
            }
        }
        
        $this->validate($request, $rules);
    }
    
    public function authenticated($request, $user)
    {
        // If user is not activated
        if (!$user->activated) {
            $uid = $user->uid;
            auth()->logout();
            return view('notActivated', ['uid' => $uid]);
        }
        
        return redirect()->intended('/');
    }
}