<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function(){
    Route::post('signup', 'AuthController@signup');
    Route::post('signin', 'AuthController@signin');
    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::post('change-password', 'AuthController@changePassword');
        Route::post('resend-verification-email', 'AuthController@resendVerificationEmail');
        Route::post('logout', 'AuthController@logout');
    });
});

    Route::group(['prefix' => 'projects', 'middleware' => 'auth:sanctum'], function (){
        Route::group(['prefix' => 'create'], function (){
            Route::post('stage-two-info', 'CreateProjectController@stageTwoInfo')->name('create-project-stage-two-info');
            Route::post('stage-three-publish', 'CreateProjectController@stageThreePublish')->name('create-project-stage-three-publish');
        });
        Route::group(['prefix' => 'edit'], function (){
            Route::post('stage-two-info/{project}', 'EditProjectController@stageTwoInfo')->name('edit-project-stage-two-info');
            Route::post('stage-three-publish/{project}', 'EditProjectController@stageThreePublish')->name('edit-project-stage-three-publish');
        });
    });



Route::get('category/main-categories', 'HelperController@mainCategories');
Route::get('category/main-categories/{category}', 'HelperController@subCategory');

Route::get('email/verify/{user}', 'AuthController@verifyEmail');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

