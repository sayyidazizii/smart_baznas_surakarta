<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware'=> ['auth:sanctum']], function(){
    Route::post('/logout', [APIController::class, 'logout']);
    Route::post('/profile', [APIController::class, 'userProfile']);
    Route::post('/change-password', [APIController::class, 'changePassword']);
    
    Route::post('/worksheet-requisition', [APIController::class, 'getWorksheetRequisition']);

    Route::post('/worksheet-result', [APIController::class, 'insertWorksheetResult']);

    Route::post('/login-state', [APIController::class, 'getLoginState']);

    Route::post('/dashboard', [APIController::class, 'getDashboard']);
    Route::get('/service-disposition', [APIController::class, 'getTransServiceDisposition']);
    Route::post('/service-disposition-detail', [APIController::class, 'getTransServiceDispositionDetail']);
    Route::post('/service-disposition-disproval', [APIController::class, 'serviceDispositionDisproval']);
    Route::post('/service-disposition-approval', [APIController::class, 'serviceDispositionApproval']);
    Route::get('/service-general', [APIController::class, 'getTransServiceGeneral']);
    Route::post('/service-general-detail', [APIController::class, 'getTransServiceGeneralDetail']);
    Route::post('/service-general-disproval', [APIController::class, 'serviceGeneralDisproval']);
    Route::post('/service-general-approval', [APIController::class, 'serviceGeneralApproval']);
});

Route::post('/login', [APIController::class, 'login']); 