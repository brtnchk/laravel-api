<?php

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

Route::post('login', 'Auth\ApiAuthController@login');
Route::post('register', 'Auth\ApiAuthController@signup');
Route::get('confirm/{token}', 'Auth\ApiAuthController@confirmEmail');

Route::post('password/reset', 'Auth\PasswordResetController@create');
Route::post('password/change', 'Auth\PasswordResetController@reset');

Route::get('/upload/{filename}', 'UploadImagesController@show');

Route::group(['middleware' => 'auth:api', 'confirmed'], function($router){
    $router->post('me', 'Auth\ApiAuthController@me');
    $router->post('logout', 'Auth\ApiAuthController@logout');
    $router->post('refresh', 'Auth\ApiAuthController@refreshAccessToken');

    $router->post('user/update', 'UserController@update');
    $router->post('user/deactivate', 'UserController@deactivate');

    $router->get('settings/data', 'SettingsController@getSettingData');
    $router->post('settings/update', 'SettingsController@updateAuthSettings');
    $router->post('settings/change-password', 'SettingsController@changePassword');

    $router->get('payment/method', 'PaymentController@index');
    $router->post('payment/method', 'PaymentController@create');
    $router->delete('payment/method/{id}', 'PaymentController@destroy');

    $router->post('/upload', 'UploadImagesController@create');
});

Route::group(['middleware' => ['auth:api', 'subscribed', 'confirmed']], function($router){

    $router->apiResources([
        'courses'            => 'CoursesController',
        'categories'         => 'CategoriesController',
        'notes'              => 'NotesController',
        'notes/{note}/items' => 'NoteItemsController'
    ]);

    $router->get('notes/{note}/stats', 'NoteStatsController@index');
    $router->post('notes/{note_id}/stats', 'NoteStatsController@store');

    $router->get('search/note/{q?}/{date_from?}/{date_to?}/{type?}/{page?}', 'NotesController@search');
    $router->get('search/course/{q?}/{date_from?}/{date_to?}/{page?}', 'CoursesController@search');

    $router->get('recent', 'ActivityController@index');
    $router->post('activity/create', 'ActivityController@create');

    $router->post('forum/topic', 'TopicController@store');
    $router->get('forum/topic/{topic_id}', 'TopicController@show');
    $router->get('forum/topic', 'TopicController@index');
    $router->post('forum/post', 'PostController@store');
    $router->post('forum/post/{post_id}/like', 'PostController@like');
    $router->post('forum/post/{post_id}/dislike', 'PostController@dislike');
});
