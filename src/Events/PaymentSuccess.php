<?php


namespace Xsimarket\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Xsimarket\Database\Models\Order;

class PaymentSuccess implements ShouldQueue
{
    /**
     * @var Order
     */

    public $order;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
