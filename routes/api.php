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

Route::group(['prefix' => 'auth', 'middleware' => ['update.last_seen']], function(){
    Route::post('signup', 'AuthController@signup');
    Route::post('login', 'AuthController@login');
    Route::get('unauthenticated', 'AuthController@unauthenticated')->name('unauthenticated');
    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::post('change-password', 'AuthController@changePassword');
        Route::post('resend-verification-email', 'AuthController@resendVerificationEmail');
        Route::post('profile/update', 'AuthController@editProfile');
        Route::post('logout', 'AuthController@logout');
    });
});

Route::group(['middleware' =>['auth:sanctum', 'update.last_seen']], function (){
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
        Route::get('orders', 'AccountController@myOrders');
        Route::get('orders/owner', 'AccountController@myOrdersOwner');
        Route::get('favourites', 'AccountController@myFavourites');
        Route::get('project-bids', 'AccountController@myProjectBids');
        Route::get('dashboard', 'AccountController@dashboard');
        Route::post('settings/vacation', 'AccountController@vacation');
        Route::get('earnings-withdrawals', 'AccountController@earningsWithdrawal');
        Route::group(['prefix' => 'notifications'], function (){
            Route::get('', 'NotificationController@userNotifications');
            Route::post('/mark-as-read/{notification}', 'NotificationController@markAsRead');
            Route::post('/mark-as-unread/{notification}', 'NotificationController@markAsUnread');
            Route::delete('/delete/{notification}', 'NotificationController@deleteNotification');
            Route::get('/unread-count', 'NotificationController@totalUnread');
            Route::post('subscribe-category', 'NotificationController@subscribeCategory')->name('category.subscribe');
            Route::post('unsubscribe-category', 'NotificationController@unsubscribeCategory')->name('category.unsubscribe');
            Route::get('subscribable-categories', 'NotificationController@subscribableCategory');
            Route::get('subscribed-categories', 'NotificationController@subscribedCategory');
        });
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
        Route::get('{project}', 'ProposalController@projectProposals')->name('project.proposals');
        Route::post('{proposal}/accept', 'ProposalController@acceptProposal')->name('proposals.accept');
        Route::post('{project}/shortlist', 'ProposalController@shortlistProposal')->name('proposals.shortlist');
        Route::get('{proposal}/shortlisted', 'ProposalController@shortlistedProposals')->name('proposals.shortlisted');
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

    // transactions

    // Route::group(['prefix' => 'trna'], function (){
    //     Route::get('/', 'ChatController@index');
    //     Route::get('messages/{receiver}', 'ChatController@fetchMessages');
    //     Route::post('messages', 'ChatController@sendMessage');
    // });

    // orders
    Route::group(['prefix' => 'orders'], function (){
        Route::get('/{order}', 'OrderController@orderDetail');
        Route::post('/{order}/requirements', 'OrderController@orderRequirements');
    });

});

Route::group(['prefix' => 'front'], function (){
    Route::get('projects', 'FrontController@allProjects');
    Route::get('projects/{project}', 'FrontController@singleProject')->name('project.details');
    Route::get('deals', 'FrontController@allDeals');
    Route::get('deals/{deal}', 'FrontController@singleDeal');
    Route::get('categories/{category}/projects', 'FrontController@categoryProjects');
    Route::get('categories/{category}/deals', 'FrontController@categoryProjects');
});

// Search
Route::group(['prefix' => 'search'], function(){
    Route::get('/', 'SearchController@all');
    Route::get('/projects', 'SearchController@projects');
    Route::get('/deals', 'SearchController@deals');
    Route::get('/freelancers', 'SearchController@users');
});



Route::get('category/main-categories', 'HelperController@mainCategories');
Route::get('category/main-categories/{category}', 'HelperController@subCategory');

Route::get('email/verify/{user}', 'AuthController@verifyEmail');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     return $request->all();
//     $request->fulfill();
//     // return redirect('/home');
// })->middleware(['auth', 'signed'])->name('verification.verify');


