<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmEmailBatchRecipient extends Model
{
    protected $table = 'crm_email_batch_recipients';
    protected $guarded = [];
    protected $casts = ['sent_at' => 'datetime'];

    public function batch()
    {
        return $this->belongsTo(CrmEmailBatch::class, 'batch_id');
    }

    public function contact()
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }
}
