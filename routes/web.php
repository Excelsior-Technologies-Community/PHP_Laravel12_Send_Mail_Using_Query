<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', [MailController::class, 'index']);
Route::post('/send-mail', [MailController::class, 'sendMail'])->name('send.mail');

Route::get('/mail-history', [MailController::class, 'history'])->name('mail.history');
Route::post('/resend-mail/{id}', [MailController::class, 'resendMail'])->name('mail.resend');
