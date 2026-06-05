<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailList extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'email', 'subject', 'message', 'status', 'sent_at', 'error_message', 'retry_count'];
    
    protected $casts = [
        'sent_at' => 'datetime',
    ];
    
    public function attachments()
    {
        return $this->hasMany(EmailAttachment::class);
    }
}