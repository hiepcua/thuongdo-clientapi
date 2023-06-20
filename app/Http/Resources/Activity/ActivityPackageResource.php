<?php

namespace App\Http\Resources\Activity;

use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'time' => TimeHelper::format($this->created_at),
            'staff' => optional($this->causer)->only(['id', 'name']),
            'content' => $this->content
        ];
    }
}
