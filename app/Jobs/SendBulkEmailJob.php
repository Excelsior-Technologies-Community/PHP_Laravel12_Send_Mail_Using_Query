<?php

namespace App\Jobs;

use App\Models\EmailList;
use App\Mail\QueryMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailIds;

    public function __construct($emailIds)
    {
        $this->emailIds = $emailIds;
    }

    public function handle()
    {
        $emails = EmailList::whereIn('id', $this->emailIds)->get();
        
        foreach ($emails as $emailRecord) {
            try {
                $details = [
                    'name' => $emailRecord->name,
                    'subject' => $emailRecord->subject,
                    'message' => $emailRecord->message,
                ];
                
                Mail::to($emailRecord->email)->send(new QueryMail($details, $emailRecord->attachments));
                
                $emailRecord->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                $emailRecord->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'retry_count' => $emailRecord->retry_count + 1,
                ]);
                
                Log::error("Email failed for {$emailRecord->email}: " . $e->getMessage());
            }
        }
    }
}