<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::group(['namespace' => 'Admin'], function(){
    // Route::post('signup', 'AdminAuthController@signup');
    // Route::post('login', 'AdminAuthController@login');

    Route::group(['middleware' => ['auth:sanctum', 'admin.auth']], function(){
        // Route::post('logout', 'AdminAuthController@logout');

        //settings
        Route::group(['prefix' => 'settings', 'namespace' => 'settings'], function(){

            //categories
            Route::group(['prefix' => 'categories'], function(){
                Route::get('list', 'CategoryController@listCategories');
                Route::post('create', 'CategoryController@createCategory')->name('admin.category.create');
                Route::put('{category}/update', 'CategoryController@updateCategory')->name('admin.category.update');
                Route::get('{category}', 'CategoryController@viewCategory');
                Route::delete('{category}/delete', 'CategoryController@deleteCategory');
                Route::get('{category}/children', 'CategoryController@childrenCategory');
            });

        });

        // deals
        Route::group(['prefix' => 'deals'], function(){
            Route::get('list', 'DealController@listDeals');
            Route::get('{deal}', 'DealController@viewDeal');
            Route::put('{deal}/change-status', 'DealController@updateStatus');
        });


    });

});
