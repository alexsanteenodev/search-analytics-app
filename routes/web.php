<?php

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

Route::get('/', 'HomeController@index')->middleware('auth');

Route::resource('search-contacts', 'SearchContactsController')->middleware('auth');
Route::post('htmlget', ['as' => 'htmlget', 'uses' => 'SearchContactsController@htmlget'])->middleware('auth');

Route::resource('url-list', 'UrlListController')->middleware('auth');

//Route::resource('webmaster', 'WebmasterController')->middleware('auth');
//Route::get('index-search', 'WebmasterController@indexSearch' )->middleware('auth');

Route::get('parse', ['as'=> 'parse', 'uses'=> 'ParseController@index' ])->middleware('auth');
Route::post('parse-save', ['as' => 'parse-save', 'uses' => 'ParseController@store'])->middleware('auth');
Route::post('keyword-get', ['as' => 'keyword-get', 'uses' => 'ParseController@keyget'])->middleware('auth');



Route::get('testcurl', ['as'=> 'testcurl', 'uses'=> 'ParseCurlController@testcurl' ])->middleware('auth');

Route::get('parse-curl', ['as'=> 'parse-curl', 'uses'=> 'ParseCurlController@index' ])->middleware('auth');
Route::post('parse-curl-save', ['as' => 'parse-curl-save', 'uses' => 'ParseCurlController@store'])->middleware('auth');
Route::post('curl-keyword-get', ['as' => 'curl-keyword-get', 'uses' => 'ParseCurlController@keyget'])->middleware('auth');


Route::group(array('prefix' => 'checker', 'middleware' => 'auth'), function()
{


    Route::get('check', ['as'=> 'check', 'uses'=> 'Checker\CheckerController@index' ]);
    Route::resource('projects', 'Checker\ProjectsController',[
        'names' => [
            'index' => 'projects',
            'edit' => 'projects.edit.{id}',
            'update' => 'projects.update.{id}',
            'create' => 'projects.create',
        ]
    ]);
    Route::resource('site-grid', 'Checker\SitesGridController',[
        'names' => [
            'index' => 'site-grid.{project_id}',
            'show' => 'site-grid.show.{id}',
            'update' => 'site-grid.update.{id}',
            'create' => 'site-grid.create.{id}',
            'gethistory' => 'site-grid.gethistory',
        ]
    ]);
});



//Auth::routes();


    Route::resource('users', 'Auth\UsersController', [
        'names' => [
            'index' => 'users',
            'destroy' => 'user.destroy'
        ]
    ])->middleware('role:admin');


Route::group(array('prefix' => 'rbac', 'middleware' => 'auth'), function()
{

    Route::resource('roles', 'Auth\Rbac\RolesController', [
        'names' => [
            'index' => 'roles',
            'destroy' => 'roles.destroy'
        ]
    ])->middleware('role:admin');


});

Route::group(['middleware' => ['web']], function() {

    Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
    Route::post('login', ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
    Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
    Route::post('password.request', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

});
