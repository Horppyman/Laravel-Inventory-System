<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\EmployeeController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/v1/register', [AuthController::class, 'register']);
    Route::post('/v1/login', [AuthController::class, 'login']);
    Route::post('/v1/logout', [AuthController::class, 'logout']);
    Route::post('/v1/refresh', [AuthController::class, 'refresh']);
    Route::get('/v1/me', [AuthController::class, 'me']);
                                             
});

Route::get('/v1/employees', [EmployeeController::class, 'index']);
Route::get('/v1/employee/{id}', [EmployeeController::class, 'show']);
Route::post('/v1/employee', [EmployeeController::class, 'store']);
Route::delete('/v1/delete-employee/{id}', [EmployeeController::class, 'destroy']);
Route::post('/v1/update-employee/{id}', [EmployeeController::class, 'update']);