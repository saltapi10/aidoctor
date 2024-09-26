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

Route::get('/chat-test', [ChatController::class, 'test']);

Route::get('/', function () {
    return view('welcome');
});

//Route::any('/ask-ai-doc',
//    [AskDocController::class, 'askDoc'])->name('askDoc');

Route::get('/token', function () {
    return csrf_token();
});

Route::get('/chat', [ChatController::class, 'chat']);
//Route::post('/chat-answer', [ChatController::class, 'chatAnswer']);
Route::post('/chat-answer-lara', [ChatController::class, 'chatGemini']);

//Route::get('/chat-gemini', [ChatController::class, 'chatGemini']);


