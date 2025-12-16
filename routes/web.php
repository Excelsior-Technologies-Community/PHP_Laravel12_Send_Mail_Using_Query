<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', [MailController::class, 'index']);
Route::post('/send-mail', [MailController::class, 'sendMail'])->name('send.mail');
