<?php

Route::group(['prefix' => 'units', 'middleware' => 'auth'], function () {
    // Unit groups
    Route::resource('unit-groups', 'UnitGroupsController');

    // Units
    Route::resource('units', 'UnitsController');
});

Route::group(['prefix' => 'items', 'middleware' => 'auth'], function () {

    Route::resource('item-categories', 'ItemCategoriesController');

    Route::get('item-categories/create/{parent}', ['uses' =>'ItemCategoriesController@create']);

    Route::resource('items', 'ItemsController');

    Route::resource('item-units', 'ItemUnitsController');

    Route::get('item-units/index/{item}', ['uses' =>'ItemUnitsController@index']);
    Route::get('item-units/create/{item}', ['uses' =>'ItemUnitsController@create']);
    Route::get('item-units/set-default/{id}', ['uses' =>'ItemUnitsController@setDefault']);
});

Route::group(['prefix' => 'stock', 'middleware' => 'auth'], function () {

    Route::resource('stock-check', 'StockCheckController');
    Route::get('autocomplete', 'StockCheckController@autocomplete');
    Route::get('history/{item}', 'StockCheckController@history');
    Route::get('modificate', 'StockCheckController@modificate');

});

Route::group(['prefix' => 'recipes', 'middleware' => 'auth'], function () {

    Route::resource('recipes', 'RecipesController');
    Route::resource('items', 'RecipeItemsController');
    Route::get('items/index/{recipe}', ['uses' =>'RecipeItemsController@index']);
    Route::get('items/create/{recipe_id}/{type}', ['uses' =>'RecipeItemsController@create']);
    Route::get('items/edit/{recipe_id}/{type}', ['uses' =>'RecipeItemsController@edit']);

});

Route::group(['prefix' => 'users', 'middleware' => 'auth'], function () {
    Route::resource('list', 'UsersController');
});
Route::group(['prefix' => 'history', 'middleware' => 'auth'], function () {
    Route::resource('list', 'HistoryController');
});
Route::group(['prefix' => 'stock-periods', 'middleware' => 'auth'], function () {
    Route::resource('list', 'StockPeriodsController');
    Route::get('close/{id}', ['uses' =>'StockPeriodsController@close']);
});
Route::group(['prefix' => 'purchases', 'middleware' => 'auth'], function () {
    Route::resource('suppliers', 'SuppliersController');
    Route::resource('list', 'PurchasesController');
});
/*Route::group(['prefix' => 'unit-groups'], function () {
    Route::get('/', 'UnitGroupsController@index');
    Route::get('create', 'UnitGroupsController@create');
    Route::get('store', 'UnitGroupsController@store');
});*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

Route::controllers([
    'password' => 'Auth\PasswordController',
]);

Route::get('/', ['middleware' => 'auth', function()
{
	return View::make('home');
}]);

Route::get('/charts', function()
{
	return View::make('mcharts');
});

Route::get('/tables', function()
{
	return View::make('table');
});

Route::get('/forms', function()
{
	return View::make('form');
});

Route::get('/grid', function()
{
	return View::make('grid');
});

Route::get('/buttons', function()
{
	return View::make('buttons');
});


Route::get('/icons', function()
{
	return View::make('icons');
});

Route::get('/panels', function()
{
	return View::make('panel');
});

Route::get('/typography', function()
{
	return View::make('typography');
});

Route::get('/notifications', function()
{
	return View::make('notifications');
});

Route::get('/blank', function()
{
	return View::make('blank');
});

Route::get('/login', function()
{
	return View::make('login');
});

Route::get('/documentation', function()
{
	return View::make('documentation');
});