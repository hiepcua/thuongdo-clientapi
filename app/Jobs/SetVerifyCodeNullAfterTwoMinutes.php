<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetVerifyCodeNullAfterTwoMinutes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Customer $_customer;

    /**
     * Create a new job instance.
     *
     * @param  Customer  $customer
     */
    public function __construct(Customer $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $this->_customer->verify_code = null;
        $this->_customer->save();
    }
}
