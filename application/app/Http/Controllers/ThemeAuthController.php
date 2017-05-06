<?php

use \Auth as Auth;
use \HelloVideo\User as User;
use \Redirect as Redirect;
use Illuminate\Contracts\Auth\PasswordBroker;

class ThemeAuthController extends BaseController {

	public function __construct()
	{
		$this->middleware('secure');
	}
	
	/*
	|--------------------------------------------------------------------------
	| Auth Controller
	|--------------------------------------------------------------------------
	*/

	public function login_form(){
		if(!Auth::guest()){
			return Redirect::to('/');
		}
		$data = array(
			'type' => 'login',
			'menu' => Menu::orderBy('order', 'ASC')->get(),
			'video_categories' => VideoCategory::all(),
			'post_categories' => PostCategory::all(),
			'theme_settings' => ThemeHelper::getThemeSettings(),
			'pages' => Page::where('active', '=', 1)->get(),
			);
		return View::make('Theme::auth', $data);
	}

	public function signup_form(){

		if(!Auth::guest()){
			return Redirect::to('/');
		}
		$data = array(
			'type' => 'signup',
			'menu' => Menu::orderBy('order', 'ASC')->get(),
			'payment_settings' => PaymentSetting::first(),
			'video_categories' => VideoCategory::all(),
			'post_categories' => PostCategory::all(),
			'theme_settings' => ThemeHelper::getThemeSettings(),
			'pages' => Page::where('active', '=', 1)->get(),
			);
		return View::make('Theme::auth', $data);
	}

	public function login(){

		$settings = \Setting::first();

	    // get login POST data
	    $email_login = array(
	        'email' => Input::get('email'),
	        'password' => Input::get('password')
	    );

	    $username_login = array(
	        'username' => Input::get('email'),
	        'password' => Input::get('password')
	    );

	    if ( Auth::attempt($email_login) || Auth::attempt($username_login) ){

	    	if($settings->free_registration && !Auth::user()->stripe_active){
    			Auth::user()->role = 'registered';
    			$user = User::find(Auth::user()->id);
    			$user->role = 'registered';
    			$user->save();
    		}

	    	if(Auth::user()->subscribed() || (Auth::user()->role == 'admin' || Auth::user()->role == 'demo') || ($settings->free_registration && Auth::user()->role == 'registered') ):

	    		$redirect = (Input::get('redirect', 'false')) ? Input::get('redirect') : '/';
	    		if(Auth::user()->role == 'demo' && Setting::first()->demo_mode != 1){
	    			Auth::logout();
	    			return Redirect::to($redirect)->with(array('note' => 'Sorry, demo mode has been disabled', 'note_type' => 'error'));
	    		} elseif($settings->free_registration && $settings->activation_email && Auth::user()->active == 0) {
	    			Auth::logout();
	    			return Redirect::to($redirect)->with(array('note' => 'Please make sure to activate your account in your email before logging in.', 'note_type' => 'error'));
	    		} else {
	    			return Redirect::to($redirect)->with(array('note' => 'You have been successfully logged in.', 'note_type' => 'success'));
	    		}
	    	else:
	    		$username = Auth::user()->username;
	    		return Redirect::to('user/' . $username . '/renew_subscription')->with(array('note' => 'Uh oh, looks like you don\'t have an active subscription, please renew to gain access to all content', 'note_type' => 'error'));
	    	endif;
	    	
	    } else {

	    	$redirect = (Input::get('redirect', false)) ? '?redirect=' . Input::get('redirect') : '';
	        // auth failure! redirect to login with errors
	        return Redirect::to('login' . $redirect)->with(array('note' => 'Invalid login, please try again.', 'note_type' => 'error'));
	    }
	    
	}

	public function signup(){

		$input = Input::all();
		$user_data = array('username' => Input::get('username'), 'email' => Input::get('email'), 'password' => Hash::make(Input::get('password')) );

		$settings = \Setting::first();
		if(!$settings->free_registration){
		
			$payment_settings = PaymentSetting::first();

			if($payment_settings->live_mode){
				User::setStripeKey( $payment_settings->live_secret_key );
			} else {
				User::setStripeKey( $payment_settings->test_secret_key );
			}
			  
			$token = Input::get('stripeToken');

			unset($input['stripeToken']);
		} else {
			if($settings->activation_email):
				$user_data['activation_code'] = str_random(60);
				$user_data['active'] = 0;
			endif;

			$user_data['role'] = 'registered';
		}

		$validation = Validator::make( $input, User::$rules );

		if ($validation->fails()){
			return Redirect::to('/signup')->with(array('note' => 'Sorry, there was an error creating your account.', 'note_type' => 'error', 'messages'))->withErrors($validation)->withInput(\Request::only('username', 'email'));
		}

	    $user = new User($user_data);
	    $user->save();

	    try{
	    	if($settings->free_registration && $settings->activation_email){
	    		
	    		Mail::send('Theme::emails.verify', array('activation_code' => $user->activation_code, 'website_name' => $settings->website_name), function($message) {
            		$message->to(Input::get('email'), Input::get('username'))->subject('Verify your email address');
       			 });

	    		return Redirect::to('/login')->with(array('note' => 'Success! One last step, be sure to verify your account by clicking on the activation link sent to your email.', 'note_type' => 'success'));

	    	} else {
	    		if(!$settings->free_registration){
	    			$user->subscription('monthly')->create($token, ['email' => $user->email]);
	    		}
	    		Auth::loginUsingId($user->id);
	    		return Redirect::to('/')->with(array('note' => 'Welcome! Your Account has been Successfully Created!', 'note_type' => 'success'));
	    	}
	    } catch(Exception $e){
	    	Auth::logout();
	    	$user->delete();
	    	return Redirect::to('/signup')->with(array('note' => 'Sorry, there was an error with your card: ' . $e->getMessage(), 'note_type' => 'error'))->withInput(\Request::only('username', 'email'));
	    }

	}

	public function verify($activation_code){
		if( ! $activation_code)
        {
            throw new InvalidConfirmationCodeException;
        }

        $user = User::where('activation_code', '=', $activation_code)->first();

        if ( ! $user)
        {
            throw new InvalidConfirmationCodeException;
        }

        $user->active = 1;
        $user->activation_code = null;
        $user->save();

        return Redirect::to('/login')->with(array('note' => 'You have successfully verified your account. Please login below.', 'note_type' => 'success'));
	}

	public function logout(){
		Auth::logout();
		return Redirect::back()->with(array('note' => 'You have been successfully logged out', 'note_type' => 'success'));
	}


	// ********** RESET PASSWORD ********** //
	public function password_reset()
	{
		$data = array(
			'type' => 'forgot_password',
			'menu' => Menu::orderBy('order', 'ASC')->get(),
			'payment_settings' => PaymentSetting::first(),
			'video_categories' => VideoCategory::all(),
			'post_categories' => PostCategory::all(),
			'theme_settings' => ThemeHelper::getThemeSettings(),
			'pages' => Page::where('active', '=', 1)->get(),
			);
		return View::make('Theme::auth', $data);
	}

	// ********** RESET REQUEST ********** //
	public function password_request()
	{
		$credentials = array('email' => Input::get('email'));
		$response = Password::sendResetLink($credentials, function($message){
			$message->subject('Password Reset Info');
		});

		switch ($response)
		{
			case PasswordBroker::RESET_LINK_SENT:
				return Redirect::to('login')->with(array('note' => trans($response), 'note_type' => 'success'));

			case PasswordBroker::INVALID_USER:
				return redirect()->back()->with(array('note' => trans($response), 'note_type' => 'error'));
		}
	}

	// ********** RESET PASSWORD TOKEN ********** //
	public function password_reset_token($token)
	{
		$data = array(
			'type' => 'reset_password',
			'token' => $token,
			'menu' => Menu::orderBy('order', 'ASC')->get(),
			'payment_settings' => PaymentSetting::first(),
			'video_categories' => VideoCategory::all(),
			'post_categories' => PostCategory::all(),
			'theme_settings' => ThemeHelper::getThemeSettings(),
			'pages' => Page::where('active', '=', 1)->get(),
			);
	  return View::make('Theme::auth', $data);
	}

	// ********** RESET PASSWORD POST ********** //
	public function password_reset_post(Request $request)
	{

		$credentials = $credentials = array('email' => Input::get('email'), 'password' => Input::get('password'), 'password_confirmation' => Input::get('password_confirmation'), 'token' => Input::get('token'));



		$response = Password::reset($credentials, function($user, $password)
		{
			$user->password = \Hash::make($password);

			$user->save();

		});

		switch ($response)
		{
			case PasswordBroker::PASSWORD_RESET:
				return Redirect::to('login')->with(array('note' => 'Your password has been successfully reset. Please login below', 'note_type' => 'success'));

			default:
				return redirect()->back()->with(array('note' => trans($response), 'note_type' => 'error'));
		}

	  
	}

}