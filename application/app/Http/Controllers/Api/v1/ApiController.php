<?php namespace Api\v1;

use \Controller as Controller;
use \View as View;

class ApiController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
		
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('api.v1.documentation');
	}

}
