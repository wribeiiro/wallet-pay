<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

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

Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction');
Route::get('/transaction', [TransactionController::class, 'index'])->name('transactions.all');
Route::get('/transaction/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
Route::delete('/transaction/{transaction}', [TransactionController::class, 'destroy'])->name('transation.destroy');
