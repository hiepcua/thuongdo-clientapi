<?php

namespace Tests\Unit;

use App\Models\OrderPackage;
use App\Scopes\CustomerCurrentScope;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class PackageTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_checkPackage()
    {
        $package = $this->getDebtCostByOrder(OrderPackage::query()->withoutGlobalScope(CustomerCurrentScope::class)->findMany(['1a7321bc-8137-4440-ba93-acc2335709a8']));
        $this->assertTrue(true);
    }


}
