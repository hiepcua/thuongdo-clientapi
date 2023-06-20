<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Constants\SupplierConstant;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'logo' => $this->logo,
            'name' => $this->name,
            'order_amount' => $this->order_amount,
            'complain_number' => $this->complain_number,
            'industry' => $this->industry,
            'address' => $this->address,
            'website' => $this->website,
            'type' => $this->type,
            'contact_type' => $this->getContactResource($this->contacts),
        ];
    }

    private function getContactResource($contacts): array
    {
        $data = [];
        foreach ($contacts as $contact) {
            $item = $contact->only(
                ['supplier_type_id', 'name', 'position', 'details']
            );
            $data[] = $item;
        }
        return $data;
    }
}
