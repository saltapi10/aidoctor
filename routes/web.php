<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AskDocController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('/ask-ai-doc',
    [AskDocController::class, 'askDoc'])->name('askDoc');

Route::post('/chat', [ChatController::class, 'chat']);

Route::get('/token', function () {
    return csrf_token();
});
