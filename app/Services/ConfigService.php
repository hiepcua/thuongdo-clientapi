<?php


namespace App\Services;


use App\Constants\ConfigConstant;
use App\Http\Resources\Config\ListConfigResource;
use App\Models\Config;

class ConfigService extends BaseService
{
    protected string $_listResource = ListConfigResource::class;

    /**
     * @param  string  $key
     * @return mixed
     */
    public function getValueByKey(string $key)
    {
        return optional(Config::query()->where('key', $key)->first())->value;
    }

    /**
     * @param  string  $key
     * @param  float  $v
     * @return mixed
     */
    public function getResultFromValueByBetweenMinMax(string $key, float $v)
    {
        $result = null;
        $values = json_decode($this->getValueByKey($key));
        if (!$values) {
            return ["first" => 0, "subsequent" => 0];
        }
        foreach ($values as $key => $value) {
            // Lấy giá trị mặc định cho dịch vụ kiểm hàng
            if (isset($value->default)) {
                $result = $value->default;
                continue;
            }
            if ((float)$value->min <= $v && (float)$value->max > $v) {
                $result = $value->result;
                break;
            }
        }
        return $result;
    }

    /**
     * @param  float  $level
     * @return mixed
     */
    public function getResultFromValueByLevel(float $level)
    {
        $values = json_decode($this->getValueByKey(ConfigConstant::CUSTOMER_LEVEL));
        foreach ($values as $key => $value) {
            if ($value->level == $level) {
                $item = $value->result;
                $item->max = $value->max;
                return $item;
            }
        }
        return null;
    }

    public function getExchangeCost(): float
    {
        return (float)$this->getValueByKey(ConfigConstant::CURRENCY_EXCHANGE_RATE);
    }

    /**
     * @param  float  $cost
     * @return null
     */
    public function getLevelByCosts(float $cost): int
    {
        $values = json_decode($this->getValueByKey(ConfigConstant::CUSTOMER_LEVEL));
        foreach ($values as $key => $value) {
            if ($value->min <= $cost && $value->max >= $cost) {
                return $value->level;
            }
        }
        return array_pop($values)->level;
    }
}