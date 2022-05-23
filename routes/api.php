<?php
// header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Methods: *');
// header('Access-Control-Allow-Headers: *');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: *');
// header('Access-Control-Allow-Headers: *');

Route::group(['namespace' => 'Api'], function () {
    Route::group(['namespace' => 'v1'], function () {

        Route::post('/login', 'AuthController@login');
        Route::post('/code-check', 'AuthController@codeCheck');
        Route::post('/register', 'AuthController@register');
        Route::post('/forgot-password', 'AuthController@forgotPassword');
        Route::post('/socialLogin', 'AuthController@socialLogin');
        Route::post('/deviceAdd', 'AuthController@addDevice');

        Route::get('/redirect-spotify', 'AuthController@spotifyRedirectURL');
        Route::get('/delete-request', 'AuthController@DeleteRequest');

        Route::post('/music/search', 'MusicController@sendFile');

        Route::get('/payment', 'PaymentController@paymentForm');
        Route::post('/payment/create', 'PaymentController@createPayment');
        Route::get('/payment/success', 'PaymentController@successPage');

        Route::get('/payment/web', 'PaymentController@paymentFormWeb');
        Route::post('/payment/create/web', 'PaymentController@createPaymentWeb');
        Route::get('/payment/success/web', 'PaymentController@successPageWeb');
        Route::get('/payment/faliure/web', 'PaymentController@failiurePageWeb');

        Route::get('/payment/failed', 'PaymentController@failiurePage');


        Route::post('/send-message', 'MessageController@sendMessage');
        Route::post('/message-view', 'MessageController@messageView');
        Route::post('/messageSendArtist', 'MessageController@artistSendMessage');
        Route::post('/message-all', 'MessageController@messageAll');


        Route::get('/company-info/{slug}', 'ContentController@getContent');
        Route::post('/contact-us', 'ContentController@contactUs');

        Route::post('/add-wallet', 'AuthController@addWallet');

        Route::group(['middleware' => 'jwt.auth'], function () {
            Route::post('/profile/update', 'AuthController@profileUpdate');
            Route::post('profile/changePassword', 'AuthController@changePassword');

            //Artist
            Route::get('/points/last', 'PaymentController@latestFundReceived');
            Route::get('/points/list', 'PaymentController@artistFundReceived');
            Route::post('/points/reedem', 'PaymentController@pointsReedem');

            Route::post('/message-list', 'MessageController@messageList');

        });

    });
});
