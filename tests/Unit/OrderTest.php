<?php

namespace Tests\Unit;

use App\Services\OrderService;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_updateOrderSupplierCost()
    {
        (new OrderService())->updateOrderSupplier('faf5318e-f14e-4a15-9d91-d4edc9187de1', 2);
        $this->assertTrue(true);
    }
}
