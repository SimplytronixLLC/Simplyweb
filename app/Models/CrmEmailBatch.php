<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmEmailBatch extends Model
{
    protected $table = 'crm_email_batches';
    protected $guarded = [];
    protected $casts = ['sent_at' => 'datetime'];

    public function recipients()
    {
        return $this->hasMany(CrmEmailBatchRecipient::class, 'batch_id');
    }
}
