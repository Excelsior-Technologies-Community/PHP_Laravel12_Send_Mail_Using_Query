<?php

namespace App\Http\Controllers;

use App\Models\EmailList;
use App\Models\EmailTemplate;
use App\Models\EmailAttachment;
use App\Mail\QueryMail;
use App\Jobs\SendBulkEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('send_mail_form', compact('templates'));
    }

    public function sendMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max
            'schedule_date' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store email record
        $emailRecord = EmailList::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('email_attachments', 'public');
                EmailAttachment::create([
                    'email_list_id' => $emailRecord->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $details = [
            'name' => $request->name,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        // Send email with or without schedule
        if ($request->schedule_date) {
            Mail::to($request->email)
                ->later(now()->diffInSeconds($request->schedule_date), 
                new QueryMail($details, $emailRecord->attachments));
            
            return back()->with('success', 'Email scheduled successfully for ' . date('Y-m-d H:i:s', strtotime($request->schedule_date)));
        } else {
            Mail::to($request->email)->queue(new QueryMail($details, $emailRecord->attachments));
            
            $emailRecord->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            
            return back()->with('success', 'Email sent successfully to ' . $request->email);
        }
    }

    public function history(Request $request)
    {
        $query = EmailList::query();
        
        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $emails = $query->orderBy('created_at', 'DESC')->paginate(10);
        $stats = [
            'total' => EmailList::count(),
            'sent' => EmailList::where('status', 'sent')->count(),
            'pending' => EmailList::where('status', 'pending')->count(),
            'failed' => EmailList::where('status', 'failed')->count(),
        ];
        
        return view('mail_history', compact('emails', 'stats'));
    }

    public function resendMail($id)
    {
        $email = EmailList::findOrFail($id);
        
        $details = [
            'name' => $email->name,
            'subject' => $email->subject,
            'message' => $email->message,
        ];

        try {
            Mail::to($email->email)->queue(new QueryMail($details, $email->attachments));
            
            $email->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
            
            return back()->with('success', 'Email resent successfully to ' . $email->email);
        } catch (\Exception $e) {
            $email->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            return back()->with('error', 'Failed to resend email: ' . $e->getMessage());
        }
    }

    public function bulkEmailForm()
    {
        return view('bulk_email_form');
    }

    public function sendBulkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipients' => 'required|array|min:1',
            'recipients.*.name' => 'required|string',
            'recipients.*.email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $emailIds = [];
        
        foreach ($request->recipients as $recipient) {
            $email = EmailList::create([
                'name' => $recipient['name'],
                'email' => $recipient['email'],
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'pending',
            ]);
            $emailIds[] = $email->id;
        }
        
        // Dispatch job to send emails in background
        SendBulkEmailJob::dispatch($emailIds);
        
        return back()->with('success', count($emailIds) . ' emails queued for sending');
    }

    public function deleteEmail($id)
    {
        $email = EmailList::findOrFail($id);
        
        // Delete attachments
        foreach ($email->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
        
        $email->delete();
        
        return back()->with('success', 'Email record deleted successfully');
    }

    public function saveTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $template = EmailTemplate::create([
            'name' => $request->template_name,
            'subject' => $request->subject,
            'content' => $request->content,
        ]);

        return response()->json(['success' => true, 'template' => $template]);
    }

    public function getTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return response()->json($template);
    }

    public function getStats()
    {
        $stats = [
            'total' => EmailList::count(),
            'sent' => EmailList::where('status', 'sent')->count(),
            'pending' => EmailList::where('status', 'pending')->count(),
            'failed' => EmailList::where('status', 'failed')->count(),
            'last_7_days' => EmailList::where('created_at', '>=', now()->subDays(7))->count(),
        ];
        
        return response()->json($stats);
    }
}