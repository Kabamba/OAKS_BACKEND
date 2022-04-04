<?php

use App\Models\User;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('users', function () {
    return User::all();
});

Route::post('login', 'AuthController@login');
Route::get('logout', 'AuthController@logout')->middleware('auth:api');
Route::post('register', 'AuthController@register');
Route::post('forgot', 'AuthController@forgot');
Route::post('reset', 'AuthController@reset');

Route::prefix('/admin')->group(function () {
    Route::get('admins/list', 'AdministrateurController@index');
    Route::get('admins/show/{id}', 'AdministrateurController@show');
    Route::post('admins/store', 'AdministrateurController@store');
    Route::post('admins/update', 'AdministrateurController@update');
    Route::post('admins/delete', 'AdministrateurController@delete');
    Route::get('admins/activate/{id}', 'AdministrateurController@activate');

    Route::get('users/list', 'UserController@index');
    Route::get('users/show/{id}', 'UserController@show');
    Route::get('users/activate/{id}', 'UserController@activate');

    Route::get('ministries/list', 'MinistryController@index');
    Route::get('ministries/limit/{id}', 'MinistryController@limit');
    Route::post('ministries/store', 'MinistryController@store');
    Route::post('ministries/update', 'MinistryController@update');
    Route::get('ministries/show/{id}', 'MinistryController@show');
    Route::get('ministries/activate/{id}', 'MinistryController@activate');
    Route::post('ministries/delete', 'MinistryController@delete');

    Route::get('sermons/list', 'SermonController@index');
    Route::get('sermons/limit/{id}', 'SermonController@limit');
    Route::get('sermons/preachers', 'SermonController@preachers');
    Route::post('sermons/search', 'SermonController@searchDate');
    Route::post('sermons/search/preacher', 'SermonController@search_preacher');
    Route::post('sermons/store', 'SermonController@store');
    Route::post('sermons/update', 'SermonController@update');
    Route::get('sermons/show/{id}', 'SermonController@show');
    Route::get('sermons/activate/{id}', 'SermonController@activate');
    Route::post('sermons/delete', 'SermonController@delete');

    Route::get('events/list', 'EventController@index');
    Route::get('events/limit/{id}', 'EventController@limit');
    Route::post('events/store', 'EventController@store');
    Route::post('events/search', 'EventController@searchDate');
    Route::post('events/update', 'EventController@update');
    Route::post('events/delete', 'EventController@delete');
    Route::post('events/delete/image', 'EventController@delete_img');
    Route::post('events/update/image', 'EventController@update_img');
    Route::get('events/show/{id}', 'EventController@show');
    Route::get('events/activate/{id}', 'EventController@activate');

    Route::get('testimonials/list', 'TestimonialController@index');
    Route::get('testimonials/limit/{id}', 'TestimonialController@limit');
    Route::post('testimonials/store', 'TestimonialController@store');
    Route::post('testimonials/search', 'TestimonialController@searchDate');
    Route::post('testimonials/delete', 'TestimonialController@delete');
    Route::post('testimonials/update', 'TestimonialController@update');
    Route::post('testimonials/delete/image', 'TestimonialController@delete_img');
    Route::post('testimonials/update/image', 'TestimonialController@update_img');
    Route::get('testimonials/show/{id}', 'TestimonialController@show');
    Route::get('testimonials/activate/{id}', 'TestimonialController@activate');

    Route::get('subscribers/list', 'SubscriberController@index');
    Route::get('subscribers/send', 'SubscriberController@send');
    Route::post('subscribers/store', 'SubscriberController@store');

    Route::get('galeries/list', 'GalerieController@index');
    Route::post('galeries/store', 'GalerieController@store');
    Route::post('galeries/update/image', 'GalerieController@update_img');
    Route::post('galeries/delete/image', 'GalerieController@delete_img');

    Route::get('stats/event', 'StatistiqueController@totEvent');
    Route::get('stats/galerie', 'StatistiqueController@totGalerie');
    Route::get('stats/sermon', 'StatistiqueController@totSermon');
    Route::get('stats/ministries', 'StatistiqueController@totMinistries');

    
});
