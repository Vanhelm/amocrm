<?php

use Illuminate\Http\Request;

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

Route::get('/lead/list', [\App\Http\Controllers\Core\LeadController::class, 'leadListAction']);
Route::get('/log/list', [\App\Http\Controllers\Core\LoggerController::class, 'logListAction']);

Route::post('/contact/link', [\App\Http\Controllers\Core\LeadController::class, 'linkContactToLead']);
