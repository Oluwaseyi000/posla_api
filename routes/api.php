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
        Route::post('profile/update', 'AuthController@editProfile');
        Route::post('logout', 'AuthController@logout');
    });
});

Route::group(['middleware' => 'auth:sanctum'], function (){
    Route::group(['prefix' => 'projects'], function (){
        Route::group(['prefix' => 'create'], function (){
            Route::post('stage-two-info', 'ProjectController@stageTwoInfo')->name('create-project-stage-two-info');
            Route::post('stage-three-publish', 'ProjectController@stageThreePublish')->name('create-project-stage-three-publish');
        });
        Route::group(['prefix' => 'edit'], function (){
            Route::post('stage-two-info/{project}', 'ProjectController@stageTwoInfoEdit')->name('edit-project-stage-two-info');
            Route::post('stage-three-publish/{project}', 'ProjectController@stageThreePublishEdit')->name('edit-project-stage-three-publish');
        });
    });

    Route::group(['prefix' => 'deals'], function (){
        Route::group(['prefix' => 'create'], function (){
            Route::post('stage-two-info', 'DealController@stageTwoInfo')->name('create-deal-stage-two-info');
            Route::post('stage-three-price/{deal}', 'DealController@stageThreePrice')->name('create-deal-stage-three-price');
            Route::post('stage-four-requirements/{deal}', 'DealController@stageFourRequirement')->name('create-deal-stage-four-requirement');
            Route::post('stage-five-publish/{deal}', 'DealController@stageFivePublish')->name('create-deal-stage-five-publish');
        });
        Route::group(['prefix' => 'edit'], function (){
            Route::post('stage-two-info/{deal}', 'DealController@stageTwoInfoEdit')->name('edit-deal-stage-two-info');
            Route::post('stage-three-price/{deal}', 'DealController@stageThreePriceEdit')->name('edit-deal-stage-three-price');
            Route::post('stage-four-requirements/{deal}', 'DealController@stageFourRequirementEdit')->name('edit-deal-stage-four-requirement');
            Route::post('stage-five-publish/{deal}', 'DealController@stageFivePublishEdit')->name('edit-deal-stage-five-publish');
        });
        Route::get('/', 'DealController@allDeal');
        Route::get('{deal}/deal_types', 'DealController@getDealTypes');
        Route::get('{deal}/deal_requirements', 'DealController@getDealRequirements');
    });

    Route::group(['prefix' => 'account'], function (){
        Route::get('deals', 'AccountController@myDeals');
        Route::get('projects', 'AccountController@myProjects');
        Route::get('profile', 'AccountController@myProfile');
        Route::get('orders', 'AccountController@myProfile');
        Route::get('favourites', 'AccountController@myFavourites');
        Route::get('project-bids', 'AccountController@myProjectBids');
        Route::get('dashboard', 'AccountController@dashboard');
        Route::post('settings/vacation', 'AccountController@vacation');
    });

      // favourites
      Route::group(['prefix' => 'favourites', 'as' => 'favourite.'], function(){
        Route::post('add-remove-deal', 'FavouriteController@addDeal')->name('add.deal');
        Route::post('add-remove-project', 'FavouriteController@addProject')->name('add.project');
    });

    // proposals
    Route::group(['prefix' => 'proposals'], function (){
        Route::post('bid', 'ProposalController@bid')->name('proposal.bid');
        Route::post('withdraw', 'ProposalController@withdraw')->name('proposal.withdraw');
    });

    // carts
    Route::group(['prefix' => 'carts'], function (){
        Route::post('payment/paystack', 'CartController@paymentPaystack')->name('payment.paystack');
        // Route::post('withdraw', 'ProposalController@withdraw')->name('proposal.withdraw');
    });

    // chats
    Route::group(['prefix' => 'chats'], function (){
        Route::get('/', 'ChatController@index');
        Route::get('messages/{receiver}', 'ChatController@fetchMessages');
        Route::post('messages', 'ChatController@sendMessage');
    });

});

Route::group(['prefix' => 'front'], function (){
    Route::get('projects', 'FrontController@allProjects');
    Route::get('projects/{project}', 'FrontController@singleProject');
    Route::get('deals', 'FrontController@allDeals');
    Route::get('deals/{deal}', 'FrontController@singleDeal');
    Route::get('categories/{category}/projects', 'FrontController@categoryProjects');
    Route::get('categories/{category}/deals', 'FrontController@categoryProjects');
});



Route::get('category/main-categories', 'HelperController@mainCategories');
Route::get('category/main-categories/{category}', 'HelperController@subCategory');

Route::get('email/verify/{user}', 'AuthController@verifyEmail');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

