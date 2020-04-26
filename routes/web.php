<?php

Route::group(['prefix'=>'admin','namespace'=>'Admin', 'as'=> 'admin.'],function(){
    Route::get('/','AuthController@showLoginForm')->name('login');
    Route::post('login','AuthController@login')->name('autenticar');
    Route::get('logout','AuthController@logout')->name('logout');
    
    //rotas protegidas
    Route::group(['middleware'=>['auth']],function(){
        Route::get('home','AuthController@home')->name('home');
        Route::get('users/team','UserController@team')->name('users.team');
        Route::resource('users','UserController' );
        
    });
    

});
