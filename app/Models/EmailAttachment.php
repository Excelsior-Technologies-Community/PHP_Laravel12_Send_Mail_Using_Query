<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAttachment extends Model
{
    use HasFactory;
    
    protected $fillable = ['email_list_id', 'file_name', 'file_path', 'file_size'];
    
    public function email()
    {
        return $this->belongsTo(EmailList::class);
    }
}