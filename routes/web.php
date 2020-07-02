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

        //rotas empresa
        Route::resource('companies','CompanyController' );
 /** Imóveis */
 Route::post('properties/image-set-cover', 'PropertyController@imageSetCover')->name('properties.imageSetCover');
 Route::delete('properties/image-remove', 'PropertyController@imageRemove')->name('properties.imageRemove');
 Route::resource('properties', 'PropertyController');

        //rotas imagems imoveis
        Route::post('properties/image-set-cover','PropertyController@imageSetCover' )->name('properties.imageSetCover');
        Route::delete('properties/image-remove','PropertyController@imageRemove' )->name('properties.imageRemove');
    });
    

});
