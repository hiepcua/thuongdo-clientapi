<?php

namespace App\Http\Resources\Complain;

use App\Helpers\MediaHelper;
use App\Helpers\TimeHelper;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\ListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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
            'cause' => [
                'avatar' => optional($this->cause)->avatar ?? MediaHelper::getFullUrlByValue('/default.png'),
                'name' => optional($this->cause)->name
            ],
            'time' => TimeHelper::format($this->created_at),
            'content' => $this->content,
            'attachments' => (new ListResource($this->attachments, AttachmentResource::class))
        ];
    }
}
