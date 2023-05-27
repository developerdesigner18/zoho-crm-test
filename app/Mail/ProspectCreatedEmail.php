<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProspectCreatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $prospectId;
    public $prospectName;
    public $prospectEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($prospectId, $prospectName, $prospectEmail)
    {
        $this->prospectId = $prospectId;
        $this->prospectName = $prospectName;
        $this->prospectEmail = $prospectEmail;
    }

    public function build()
    {
        $zohoCRMUrl = 'https://www.zohoapis.in/crm/';
        $prospectDetailsUrl = $zohoCRMUrl . 'v2/Leads/' . $this->prospectId;

        return $this->view('emails.prospect-created')
            ->with([
                'prospectId' => $this->prospectId,
                'prospectName' => $this->prospectName,
                'prospectEmail' => $this->prospectEmail,
                'prospectDetailsUrl' => $prospectDetailsUrl,
            ])
            ->subject('New Prospect Created');
    }
}
