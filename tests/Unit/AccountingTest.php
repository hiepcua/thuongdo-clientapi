<?php

namespace Tests\Unit;

use App\Constants\LocateConstant;
use App\Models\CustomerDelivery;
use App\Models\Order;
use App\Models\Warehouse;
use App\Services\AccountingService;
use App\Services\OrderService;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountingTest extends TestCase
{
    private AccountingService $_service;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_service = new AccountingService();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testInspectionCost(): void
    {
        $this->assertTrue(
            is_numeric(
                $this->_service->getInspectionCost(
                    optional(Order::query()->inRandomOrder()->first())->id ?? getUuid()
                )
            )
        );
    }

    /**
     *
     */
    public function testWoodworkingCost(): void
    {
        $this->assertTrue(
            is_numeric(
                $this->_service->getWoodworkingCost(mt_rand(), mt_rand())
            )
        );
    }

    public function testOrderFeeCost(): void
    {
        $this->assertTrue(
            is_numeric(
                $this->_service->getOrderFee(mt_rand())
            )
        );
    }

    public function testCustomerLevelCost(): void
    {
        $this::assertTrue(
            is_numeric(
                $this->_service->getDiscountCost(mt_rand(), 2)
            )
        );
    }

    public function testCalculatorServiceCost()
    {
        $data = [
            "inspection_cost" => 1,
            "woodworking_cost" => 1,
            "discount_cost" => 1,
            "order_fee" => 1,
            "delivery_type" => "normal",
            "is_inspection" => true,
            "is_woodworking" => false,
            "is_shock_proof" => true,
            "warehouse_id" => Warehouse::query()->where('country', LocateConstant::COUNTRY_VI)->first()->id,
            "customer_delivery_id" => CustomerDelivery::query()->first()->id,
            "products" => [
                [
                    "name" => "required|max:255",
                    "url" => "https://google.com",
                    "image" => "/upload/a.jpg",
                    "note" => "Quỷ xứ",
                    "unit_price_cny" => 1,
                    "quantity" => 1
                ]
            ]
        ];
        (new OrderService())->prepareStore($data);
        self::assertTrue(is_array($data));
    }

    public function testInternationalShippingCost()
    {
        $this::assertTrue(
            is_numeric((new AccountingService())->getInternationShippingCost(rand(0, 500), rand(0, 500)))
        );
    }

    public function test_depositCost()
    {
        $order = Order::query()->withoutGlobalScope(CustomerCurrentScope::class)->find(
            'ffc15b20-fa8c-464a-b297-f8aaa5a4c395'
        );
        dd((new TransactionService())->checkWallet($order->total_amount, 90, 1000000000));
    }

}
