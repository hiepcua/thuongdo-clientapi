<?php

namespace App\Http\Controllers;

use App\Constants\ComplainConstant;
use App\Helpers\PaginateHelper;
use App\Http\Resources\Complain\FeedbackResource;
use App\Http\Resources\ListResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Models\Complain;
use App\Models\ComplainFeedback;
use App\Models\ComplainFeedbackAttachment;
use App\Models\Customer;
use App\Services\ComplainFeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FeedbackController extends Controller implements StoreValidationInterface
{
    public function __construct(ComplainFeedbackService $service)
    {
        $this->_service = $service;
    }

    public function store(): JsonResponse
    {
        $this->throwValidation(__FUNCTION__);
        $complainId = request('complainId');
        Complain::query()->findOrFail($complainId);
        $params = request()->all();
        $params['complain_id'] = $complainId;
        $params['cause_type'] = Customer::class;
        $params['cause_id'] = Auth::user()->id;
        $feedback = $this->_service->store($params);
        $attachments = [];
        foreach ($params['attachments'] as $attachment) {
            $attachments[] = [
                'id' => getUuid(),
                'complain_id' => $complainId,
                'complain_feedback_id' => $feedback->id,
                'attachment_id' => $attachment
            ];
        }
        ComplainFeedbackAttachment::query()->insert($attachments);
        return resSuccessWithinData(new FeedbackResource($feedback));
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'content' => 'required|max:500',
            'attachments' => 'array',
            'attachments.*' => 'required|exists:attachments,id',
        ];
    }

    protected function getAttributes(): array
    {
        return [
            'content' => 'Phản hồi',
            'attachments' => 'Tập tin đính kèm',
            'attachments.*' => 'Tập tin'
        ];
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $complain = request('complainId');
        $feedbacks = ComplainFeedback::query()->where(
            ['complain_id' => $complain, 'type' => ComplainConstant::NOTE_PUBLIC]
        )->limit(PaginateHelper::getLimit())->get();
        return resSuccessWithinData(new ListResource($feedbacks, FeedbackResource::class));
    }
}
