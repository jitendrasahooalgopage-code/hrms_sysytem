<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AssetRequestSubmitted extends Mailable
{
    public $assetRequest;

    public function __construct($assetRequest)
    {
        $this->assetRequest = $assetRequest;
    }

    public function build()
    {
        return $this
            ->subject('New Asset Request')
            ->view('emails.asset_request');
    }
}