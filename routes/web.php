<?php

use App\Http\Controllers\ApiUserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SubscriberController;
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

Route::get('/', [ApiUserController::class,'index'])->middleware('serviceGuest')->name('index');
Route::post('/', [ApiUserController::class,'validateKey'])->name('validate-api-key');
Route::group(['middleware' => 'hasAPIKey'],function(){
    Route::get('/add-group',[GroupController::class,'showAddGroupForm'])->name('show-add-group');
    Route::post('/store-group',[GroupController::class,'storeNewGroup'])->name('store-group');
});
Route::group(['middleware' => ['hasAPIKey','hasGroup']],function(){
    Route::get('/subscribers',[SubscriberController::class,'index'])->name('subscribers');
    Route::get('/get-subscribers-list',[SubscriberController::class,'getSubscribers'])->name('paginated-subscribers');
    Route::get('/add-subscriber',[SubscriberController::class,'showAddSubscriberForm'])->name('show-add-subscriber');
    Route::post('/store-subscriber',[SubscriberController::class,'storeNewSubscriber'])->name('store-subscriber');
    Route::get('/edit-subscriber/{email}',[SubscriberController::class,'showSubscriberEditForm'])->name('show-edit-subscriber');
    Route::post('/update-subscriber/{email}',[SubscriberController::class,'updateSubscriber'])->name('update-subscriber');
    Route::post('/delete-subscriber',[SubscriberController::class,'deleteSubscriber'])->name('delete-subscriber');
});
