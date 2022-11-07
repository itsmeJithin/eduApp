<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;


class OTPSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    public $phoneNumber;

    /**
     * Create a new job instance.
     *
     * @param string $phoneNumber
     */
    public function __construct($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $twilio = new Client(config('app.twilio.SID'), config('app.twilio.TOKEN'));
            $twilio->verify->v2->services(config('app.twilio.TWILIO_VERIFICATION_SID'))
                ->verifications
                ->create('+91' . $this->phoneNumber, "sms");
        } catch (\Exception $e) {
            $this->failed($e);
        }
    }

    public function failed($exception = null)
    {
        error_log($exception->getMessage());
        // Handle exception case
    }
}
