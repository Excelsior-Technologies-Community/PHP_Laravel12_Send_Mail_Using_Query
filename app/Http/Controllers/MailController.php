<?php

// app/Http/Controllers/MailController.php

namespace App\Http\Controllers;

use App\Models\EmailList;
use App\Mail\QueryMail;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function index()
    {
        return view('send_mail_form');
    }

public function sendMail(Request $request)
{
    // Validate the form data
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $details = [
        'name' => $request->name,
        'subject' => $request->subject,
        'message' => $request->message,
    ];

    // Store email in DB
    EmailList::create([
        'name' => $request->name,
        'email' => $request->email,
        'subject' => $request->subject,
        'message' => $request->message,
    ]);

    // Send email using queue
    Mail::to($request->email)->queue(new QueryMail($details));

    return back()->with('success', 'Email sent successfully to ' . $request->email);
}

}

