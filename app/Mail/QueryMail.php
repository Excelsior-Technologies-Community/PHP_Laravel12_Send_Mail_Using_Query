<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class QueryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $details;
    public $attachments_data;

    public function __construct($details, $attachments_data = null)
    {
        $this->details = $details;
        $this->attachments_data = $attachments_data;
    }

    public function build()
    {
        $mail = $this->subject($this->details['subject'])
                    ->view('emails.queryMail');
        
        // Add attachments if exist
        if ($this->attachments_data) {
            foreach ($this->attachments_data as $attachment) {
                $mail->attach(Storage::path($attachment->file_path), [
                    'as' => $attachment->file_name
                ]);
            }
        }
        
        return $mail;
    }
}