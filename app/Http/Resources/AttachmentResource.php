<?php

namespace App\Http\Resources;

use App\Helpers\MediaHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'url' => MediaHelper::getDomain($this->attachment_id),
            'extension' => optional($this->file)->extension,
            'name' => optional($this->file)->name,
            'type' => optional($this->file)->type
        ];
    }
}
