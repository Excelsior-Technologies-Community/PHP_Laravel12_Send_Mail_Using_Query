<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', [MailController::class, 'index'])->name('mail.form');
Route::post('/send-mail', [MailController::class, 'sendMail'])->name('send.mail');

Route::get('/mail-history', [MailController::class, 'history'])->name('mail.history');
Route::post('/resend-mail/{id}', [MailController::class, 'resendMail'])->name('mail.resend');
Route::delete('/delete-mail/{id}', [MailController::class, 'deleteEmail'])->name('mail.delete');

Route::get('/bulk-email', [MailController::class, 'bulkEmailForm'])->name('bulk.form');
Route::post('/bulk-email', [MailController::class, 'sendBulkEmail'])->name('bulk.send');

Route::post('/save-template', [MailController::class, 'saveTemplate'])->name('template.save');
Route::get('/get-template/{id}', [MailController::class, 'getTemplate'])->name('template.get');
Route::get('/email-stats', [MailController::class, 'getStats'])->name('email.stats');