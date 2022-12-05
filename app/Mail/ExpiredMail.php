<?php

namespace App\Mail;

use App\Enums\StatusEnum;
use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public array|null $config ;
    public Collection $apps_id ;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Collection $apps_id , array|null $config = null)
    {
        $this->config = $config;
        $this->apps_id = $apps_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $apps = App::whereIn('id' , $this->apps_id->toArray())->get();
        return $this->subject($this->config['email']['subject'])
            ->markdown('emails.expired')
            ->with([
                'apps'=>$apps,
                'from_status'=> StatusEnum::toString( $this->config['email_when']['from'] ),
                'to_status'=> StatusEnum::toString( $this->config['email_when']['to'] )
            ]);
    }
}
