<?php

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

        EmailList::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        Mail::to($request->email)->queue(new QueryMail($details));

        return back()->with('success', 'Email sent successfully to ' . $request->email);
    }

    public function history()
    {
        $emails = EmailList::orderBy('created_at', 'ASC')->paginate(3);
        return view('mail_history', compact('emails'));
    }

    public function resendMail($id)
    {
        $email = EmailList::findOrFail($id);

        $details = [
            'name' => $email->name,
            'subject' => $email->subject,
            'message' => $email->message,
        ];

        Mail::to($email->email)->queue(new QueryMail($details));

        return back()->with('success', 'Email resent successfully to ' . $email->email);
    }
}
