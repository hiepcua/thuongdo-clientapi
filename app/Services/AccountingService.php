<?php


namespace App\Services;


use App\Constants\ConfigConstant;
use App\Constants\LocateConstant;
use App\Helpers\AccountingHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AccountingService extends BaseService
{
    private ConfigService $_configService;

    public function __construct()
    {
        parent::__construct();
        $this->_configService = new ConfigService();
    }

    /**
     * @param  array  $data
     */
    public function calculatorServiceCost(array &$data)
    {
        $data['exchange_rate'] = (float)(new ConfigService())->getValueByKey(ConfigConstant::CURRENCY_EXCHANGE_RATE);
        $data['order_cost'] = $this->getTotalOrderInProducts($data['products'], $data['exchange_rate']);
        $data['inspection_cost'] = $data['is_inspection'] ? $this->getInspectionCost(array_sum(Arr::pluck($data['products'], 'quantity'))) : 0;
        $data['order_fee'] = $this->getOrderFee($data['order_cost'], $orderPercent);
        $data['order_percent'] = $orderPercent;
        $data['discount_cost'] = $this->getDiscountCost($data['order_fee']);
        $data['total_amount'] =  $data['order_cost'] + $data['inspection_cost'] - $data['discount_cost'] + $data['order_fee'];
    }

    /**
     * Tính phí dịch vụ kiểm hàng
     * @param  int  $productsNumber
     * @return float
     */
    public function getInspectionCost(int $productsNumber): float
    {
        return AccountingHelper::getCosts((float)$this->_configService->getResultFromValueByBetweenMinMax(
            ConfigConstant::SERVICE_INSPECTION,
            $productsNumber
        ) * $productsNumber);
    }

    /**
     * @param  float  $weight
     * @param  float  $volume
     * @param  bool  $isWeightGreaterThanVolume
     * @return float
     */
    public function getWoodworkingCost(float $weight, float $volume, &$isWeightGreaterThanVolume = true): float
    {
        if($weight == 0 && $volume == 0) return 0;
        $weightJson = json_decode($this->_configService->getValueByKey(ConfigConstant::SERVICE_WOODWORKING_WEIGHT));
        $cost = $this->getCostByFirstSubsequent($weightJson, $weight);
        $volumeJson = $this->_configService->getResultFromValueByBetweenMinMax(
            ConfigConstant::SERVICE_WOODWORKING_VOLUME,
            $volume
        );
        $volumeCost = $this->getCostByFirstSubsequent($volumeJson, $volume);
        return ($isWeightGreaterThanVolume = $cost > $volumeCost) ? $cost : $volumeCost;
    }

    /**
     * Phí vận chuyển quốc tế (Trung Quốc)
     * @param  float  $weight
     * @param  float  $volume
     * @return float
     */
    public function getInternationShippingCost(float $weight, float $volume): float
    {
        $warehouseService = new WarehouseService();
        $province = $warehouseService->getProvinceById(
            $warehouseService->getWarehouseRandomByCountry(LocateConstant::COUNTRY_VI)->id
        );
        $weightCost = $this->getCostByProvince(ConfigConstant::SERVICE_PACKAGE_SHIPPING_WEIGHT, $weight, $province);
        $volumeCost = $this->getCostByProvince(ConfigConstant::SERVICE_PACKAGE_SHIPPING_VOLUME, $volume, $province);
        return AccountingHelper::getCosts($weightCost > $volumeCost ? $weightCost : $volumeCost);
    }

    /**
     * @param  string  $key
     * @param  float  $value
     * @param  string  $province
     * @return float
     */
    private function getCostByProvince(string $key, float $value, string $province): float
    {
        $json = $this->_configService->getResultFromValueByBetweenMinMax($key, $value);
        return $json ? $json->{$province} : 0;
    }

    /**
     * @param  float  $totalOrder
     * @param  float|null  $orderPercent
     * @return float
     */
    public function getOrderFee(float $totalOrder, ?float &$orderPercent = 0): float
    {
        $percent = $this->_configService->getResultFromValueByBetweenMinMax(
            ConfigConstant::SERVICE_ORDER_FEE,
            $totalOrder
        );
        $default = json_decode($this->_configService->getValueByKey(ConfigConstant::SERVICE_ORDER_FEE), true)[0]['default'];
        // Percent mà > 100 thì nó sẽ là giá trị tối thiểu đặt hàng 9000/đơn
        $orderPercent = $percent > 100 ? 3 : $percent;
        return AccountingHelper::getCosts($percent > 100  ? $default : max(($totalOrder * ($percent / 100)), $default));
    }


    /**
     * @param  float  $totalOrder
     * @param  int  $level
     * @return float
     */
    public function getCustomerLevelCost(float $totalOrder, int $level): float
    {
        $level = $level === 0 ? ($level + 1) : $level;
        return AccountingHelper::getCosts(
            $totalOrder * $this->getPercent($this->_configService->getResultFromValueByLevel($level)->order)
        );
    }

    /**
     * @param  float  $orderCost
     * @return float
     */
    public function getInsuranceCost(float $orderCost): float
    {
        return AccountingHelper::getCosts(
            $orderCost * $this->getPercent(
                (float)$this->_configService->getValueByKey(ConfigConstant::SERVICE_INSURANCE)
            )
        );
    }

    /**
     * @param  float|null  $number
     * @return float|int
     */
    public function getPercent(?float $number): float
    {
        return ($number ?? 0) / 100;
    }

    /**
     * @param $item
     * @param  float  $value
     * @return float
     */
    private function getCostByFirstSubsequent($item, float $value): float
    {
        return $item ? (float)$item->first + (float)$item->subsequent * ($value - 1) : 0;
    }

    /**
     * @param  array  $products
     * @param  float  $exchangeRate
     * @return float
     */
    private function getTotalOrderInProducts(array $products, float $exchangeRate): float
    {
        $results = 0;
        foreach ($products as $product) {
            $results += $product['quantity'] * $product['unit_price_cny'];
        }
        return AccountingHelper::getCosts($results * $exchangeRate);
    }

    /**
     * @param  float  $orderCost
     * @return float
     */
    public function getDiscountCost(float $orderCost): float
    {
        return $this->getCustomerLevelCost($orderCost, optional(Auth::user())->level ?? 1);
    }

    /**
     * @param  float  $weight
     * @return float
     */
    public function getShockCost(float $weight): float
    {
        $shockJson = json_decode($this->_configService->getValueByKey(ConfigConstant::SERVICE_SHOCK_PROOF));
        return $this->getCostByFirstSubsequent($shockJson, $weight);
    }
}