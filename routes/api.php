<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatoController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('v1/lead', [CandidatoController::class, 'createLead'])->middleware('jwt.auth');
Route::get('v1/lead/{id}', [CandidatoController::class, 'lead'])->middleware('jwt.auth');
Route::get('v1/leads', [CandidatoController::class, 'leads'])->middleware('jwt.auth');
//Route::post('login', 'AuthController@login');

Route::post('login', [AuthController::class, 'login']);

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
