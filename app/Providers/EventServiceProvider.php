<?php

namespace App\Providers;

use App\Models\CartDetail;
use App\Models\Complain;
use App\Models\ComplainFeedback;
use App\Models\Consignment;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderDetailNote;
use App\Models\OrderPackage;
use App\Models\Transaction;
use App\Observers\CartDetailObserver;
use App\Observers\ComplainObserve;
use App\Observers\ConsignmentObserve;
use App\Observers\CustomerObserver;
use App\Observers\DeliveryObserve;
use App\Observers\FeedbackObserve;
use App\Observers\OrderDetailNoteObserve;
use App\Observers\OrderObserve;
use App\Observers\OrderPackageObserve;
use App\Observers\TransactionObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Customer::observe(CustomerObserver::class);
        CartDetail::observe(CartDetailObserver::class);
        Order::observe(OrderObserve::class);
        Complain::observe(ComplainObserve::class);
        Delivery::observe(DeliveryObserve::class);
        OrderPackage::observe(OrderPackageObserve::class);
        Consignment::observe(ConsignmentObserve::class);
        ComplainFeedback::observe(FeedbackObserve::class);
        OrderDetailNote::observe(OrderDetailNoteObserve::class);
        Transaction::observe(TransactionObserver::class);
    }
}
