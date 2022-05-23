<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/login', 'LoginController@showLogin')->name('login');
Route::post('/doLogin', 'LoginController@login');
Route::post('/logout', 'LoginController@logout')->name('logout');

Route::get('/privacy-policy', 'LoginController@privacyPolicy');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/user', 'UserController@index');
Route::get('/user/not-registeres', 'UserController@unregisteredUser');
Route::get('/user/add', 'UserController@add');
Route::post('/user/insert', 'UserController@insert');
Route::get('/user/edit/{id}', 'UserController@edit');
Route::post('/user/update', 'UserController@update');
Route::get('/user/delete/{id}', 'UserController@delete');
Route::get('/user/statusChange/{id}/{Active}', 'UserController@changeStatus');

Route::get('/artist', 'ArtistController@index');
Route::get('/accessArtist', 'ArtistController@accessArtist');
Route::get('/artist/add', 'ArtistController@add');
Route::post('/artist/insert', 'ArtistController@insert');
Route::get('/artist/edit/{id}', 'ArtistController@edit');
Route::post('/artist/update', 'ArtistController@update');
Route::get('/artist/delete/{id}', 'ArtistController@delete');
Route::get('/artist/statusChange/{id}/{active}', 'ArtistController@changeStatus');

Route::get('/content', 'ContentController@index');
Route::get('/content/add', 'ContentController@add');
Route::post('/content/insert', 'ContentController@insert');
Route::get('/content/edit/{id}', 'ContentController@edit');
Route::post('/content/update', 'ContentController@update');
Route::get('/content/delete/{id}', 'ContentController@delete');

Route::get('/transaction', 'TransactionController@index');



// Route::get('/karma-points', 'KarmaController@index');
// Route::get('/karma-points/add', 'KarmaController@add');
// Route::post('/karma-points/insert', 'KarmaController@insert');
// Route::get('/karma-points/edit/{id}', 'KarmaController@edit');
// Route::post('/karma-points/update', 'KarmaController@update');
// Route::get('/karma-points/delete/{id}', 'KarmaController@delete');




Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
