<?php namespace HelloVideo\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
	    $settings = \Setting::first();
	    $theme = $settings->theme;
	    $theme_settings = \ThemeHelper::getThemeSettings();
	    $menu = \Menu::orderBy('order', 'ASC')->get();
	    $video_categories = \VideoCategory::all();
	    $post_categories = \PostCategory::all();
	    $pages = \Page::all();
		//
		view()->share('settings', $settings);
		view()->share('theme', $theme);
		view()->share('theme_settings', $theme_settings);
		view()->share('menu', $menu);
		view()->share('video_categories', $video_categories);
		view()->share('post_categories', $post_categories);
		view()->share('pages', $pages);

	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'HelloVideo\Services\Registrar'
		);
	}

}
