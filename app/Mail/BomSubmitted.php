<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BomSubmitted extends Mailable
{
    use SerializesModels;

    public $data;
    public $filePath;

    public function __construct($data, $filePath)
    {
        $this->data = $data;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('New BOM Submission - '.$this->data['bom_id'])
                    ->markdown('emails.bom_submitted')
                    ->attach($this->filePath);
    }
}