<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportStatusResource extends JsonResource
{
    private ?int $_quantity;
    private string $_constant;

    public function __construct($resource, string $constant, ?int $quantity = null)
    {
        $this->_quantity = $quantity;
        $this->_constant = $constant;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $status = $this->resource;
        $index = array_search($status, array_keys($this->_constant::STATUSES)) ?? 0;
        $data = [
            'value' => $status,
            'name' => $this->_constant::STATUSES[$status],
            'color' => $this->_constant::STATUSES_COLOR[$index]
        ];
        if (!is_null($this->_quantity)) {
            $data['quantity'] = $this->_quantity;
        }
        return $data;
    }
}
