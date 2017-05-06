<?php 

Route::get('hello', function(){

	// Show the file located in /content/plugins/hello/views/index.php
	return View::make('plugins::hello.views.index');

});