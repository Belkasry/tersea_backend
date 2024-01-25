<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\HistoricController;
use App\Http\Controllers\API\ValRefController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::put('password', [AuthController::class, 'updatePassword']);
});

Route::get('password/check-token', [AdminController::class, 'getUserByToken']);
Route::get('valref/sectors', [ValRefController::class, 'sectors']);
Route::post('password/reset', [AdminController::class, 'resetPassword']);

Route::middleware(['check.role:admin'])->group(function () {
    Route::post('password/reset-link', [AdminController::class, 'sendResetLink']);
    Route::get('admins', [AdminController::class, 'listAdmins']);
    Route::post('admins', [AdminController::class, 'addAdmin']);
    Route::delete('admins/{id}', [AdminController::class, 'destroy']);
    Route::put('/admins/{id}/status', [AdminController::class, 'changeUserStatus']);
    Route::put('/employees/{id}/status', [EmployeeController::class, 'changeUserStatus']);
    Route::put('/admins/{id}', [AdminController::class, 'editAdmin']);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::get('employees/{employee}/collegues', [EmployeeController::class, 'getCollegues']);
    Route::apiResource('historics', HistoricController::class);

});

Route::middleware(['check.role:employee'])->group(
    function () {
        Route::get('me/collegues', [EmployeeController::class, 'getMyCollegues']);
        Route::get('me', [EmployeeController::class, 'showMe']);
        Route::put('me', [EmployeeController::class, 'updateMe']);
    });

