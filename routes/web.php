<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\Auth\ZohoOAuthController;


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
    return view('welcome');
});

Route::get('zoho', [LeadsController::class,'getRecentProspects']);
Route::get('zoho2', [LeadsController::class,'getLeads']);
Route::post('storeProspect', [LeadsController::class,'storeProspect']);

Route::get('/oauth', [ZohoOAuthController::class, 'redirectToZoho']);
Route::get('/oauth/callback', [ZohoOAuthController::class, 'handleCallback']);

// Leads Routes
