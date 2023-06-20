<?php


namespace App\Services;


use App\Http\Resources\Complain\FeedbackResource;

class ComplainFeedbackService extends BaseService
{
    protected string $_resource = FeedbackResource::class;
}