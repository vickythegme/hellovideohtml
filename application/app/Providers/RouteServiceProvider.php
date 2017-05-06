<?php namespace HelloVideo\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use \Auth as Auth;
use \View as View;
use \Redirect as Redirect;
use \Plugin as Plugin;
use \PluginData as PluginData;
use \Input as Input;
use Illuminate\Cookie\CookieJar as CookieJar;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = null;

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		parent::boot($router);

		//
		$settings = \Setting::first();
		$root_dir = __DIR__ . '/../../../';
	    
	        if(\Cookie::get('theme')){
	            $theme = \Crypt::decrypt(\Cookie::get('theme'));
	            define('THEME', $theme);
	        } else {
	            if($settings->theme): 
	                $theme = $settings->theme;
	            	if (!defined('THEME')) define('THEME', $theme);
	            endif;
	        }
		

		\Config::set('mail.from', ['address' => $settings->system_email, 'name' => $settings->website_name]);

		@include( $root_dir . 'content/themes/' . $theme . '/functions.php');
		View::addNamespace('Theme', $root_dir . 'content/themes/' . $theme);

		View::addNamespace('plugins', 'content/plugins/');

		try{

			$plugins = Plugin::where('active', '=', 1)->get();
        
            //print_r($plugins); die();
            foreach($plugins as $plugin){
                $plugin_name = $plugin->slug;
                $include_file = 'content/plugins/' . $plugin_name . '/functions.php';
                
                // Create Settings Route for Plugin
        
                Route::group(array('before' => 'admin'), function(){
    
                    Route::get( 'admin/plugin/{plugin_name}', function($plugin_name){
                        $plugin_data = PluginData::where('plugin_slug', '=', $plugin_name)->get();

                         $data = array();

                        foreach($plugin_data as $plugin):
							$data[$plugin->key] = $plugin->value;
						endforeach;


                        return View::make( 'plugins::' . $plugin_name . '.settings', $data);
                    });
            
                    Route::post( 'admin/plugin/{plugin_name}', function($plugin_name){
                        $input = Input::all();
                        foreach($input as $key => $value){
                            $pluginData = PluginData::where('plugin_slug', '=', $plugin_name)->where('key', '=', $key)->first();
                            
                            if(!empty($pluginData->id)){
                                $pluginData->value = $value;
                                $pluginData->save();
                            } else {
                                $pluginData = new PluginData;
                                $pluginData->plugin_slug = $plugin_name;
                                $pluginData->key = $key;
                                $pluginData->value = $value;
                                $pluginData->save();
                            }
                        }
                        
                        return Redirect::to( '/admin/plugin/' . $plugin_name )->with(array('note' => 'Successfully updated plugin information', 'note_type' => 'success') );
                    });
            
                });
        
                if(file_exists($include_file)){

                   include($include_file);
                }
        
            }

		} catch(Exception $e){
			die('error in RouteServiceProvider.php with plugins');
		}
		

		Route::filter('auth', function()
		{
			if (Auth::guest()) return Redirect::guest('login');
		});


		Route::filter('auth.basic', function()
		{
			return Auth::basic();
		});

		Route::filter('if_logged_in_must_be_subscribed', function()
		{
			
			if (!Auth::guest()){
				$settings = \Setting::first();
				$free_registration = $settings->free_registration;

				if( (!Auth::user()->subscribed() && Auth::user()->role == 'subscriber') || (!$free_registration && Auth::user()->role == 'registered') ){
					$username = Auth::user()->username;
			    	return Redirect::to('user/' . $username . '/renew_subscription')->with(array('note' => 'Uh oh, looks like you don\'t have an active subscription, please renew to gain access to all content', 'note_type' => 'error'));
				}		
			}
		});

		Route::filter('admin', function()
		{
			if (!Auth::guest() && (Auth::user()->role == 'admin' || Auth::user()->role == 'demo')){

			} else {
				return Redirect::to('/login');
			}
		});

		Route::filter('demo', function()
		{
			if (!Auth::guest() && Auth::user()->role == 'demo'){
				return Redirect::back()->with(array('note' => 'Sorry, unfortunately this functionality is not available in demo accounts', 'note_type' => 'error'));
			}
		});

		Route::filter('guest', function()
		{
			if (Auth::check()) return Redirect::to('/');
		});

	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
