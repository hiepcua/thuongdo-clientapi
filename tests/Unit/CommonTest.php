<?php

namespace Tests\Unit;

use App\Models\OrderDetail;
use App\Scopes\CustomerCurrentScope;
use App\Services\OrderService;
use Tests\TestCase;

class CommonTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCommon()
    {
        $details = OrderDetail::query()->withoutGlobalScope(CustomerCurrentScope::class)->where(
            ['order_id' => 'eb412a62-dac4-42a6-a427-d773b648ebb1']
        )->groupBy('supplier_id')->selectRaw(
            "sum(amount_cny) as amount_cny, sum(quantity) as quantity, supplier_id"
        )->get();
        $this->assertTrue(true);
    }
}
