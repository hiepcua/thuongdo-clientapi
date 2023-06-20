<?php


namespace App\Services;


use App\Constants\ActivityConstant;
use App\Constants\OrderConstant;
use App\Http\Resources\Activity\ActivityPackageResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\ReportStatusResource;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ActivityService extends BaseService
{
    protected string $_resource = ActivityResource::class;

    /**
     * @param  int  $perPage
     * @return JsonResponse
     * @throws \Exception
     */
    public function pagination(int $perPage): JsonResponse
    {
        return parent::pagination($perPage);
    }

    /**\
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getOrderLog(string $orderId): JsonResponse
    {
        $activities = Activity::query()->where(
            ['log_name' => ActivityConstant::ORDER_LOG, 'object_id' => $orderId]
        )->get();
        return resSuccessWithinData(
            $activities->transform(
                function ($item) {
                    return new $this->_resource($item);
                }
            )
        );
    }

    /**
     * @param  Model  $subject
     * @param  string  $content
     * @param  string  $logName
     * @param  string|null  $objectId
     * @param  string|null  $properties
     */
    public function setLog(Model $subject, string $content, string $logName, ?string $objectId = null, ?string $properties = null)
    {
        /** @var Customer $user */
        $user = Auth::user();
        Activity::query()->create(
            [
                'subject_type' => get_class($subject),
                'subject_id' => $subject->id,
                'causer_type' => get_class($user),
                'causer_id' => $user->id,
                'log_name' => $logName,
                'content' => $content,
                'object_id' => $objectId,
                'organization_id' => $user->organization_id,
                'properties' => $properties
            ]
        );
    }

    /**
     * @param  Model  $subject
     * @param  string  $content
     * @param  string  $orderId
     */
    public function setOrderLog(Model $subject, string $content, string $orderId)
    {
        $data = new ReportStatusResource(Order::query()->find($orderId)->status, OrderConstant::class);
        $this->setLog($subject, $content, ActivityConstant::ORDER_LOG, $orderId, json_encode($data));
    }

    /**
     * @param  Model  $subject
     * @param  string  $content
     * @param  string  $orderId
     */
    public function setConsignmentLog(Model $subject, string $content, string $orderId)
    {
        $this->setLog($subject, $content, ActivityConstant::CONSIGNMENT_LOG, $orderId);
    }

    /**
     * @param  Model  $subject
     * @param  string  $content
     * @param  string  $orderId
     */
    public function setConsignment(Model $subject, string $content, string $orderId)
    {
        $this->setLog($subject, $content, ActivityConstant::CONSIGNMENT_LOG, $orderId);
    }

    /**
     * @param  string  $packageId
     * @return JsonResponse
     */
    public function getPackageLog(string $packageId): JsonResponse
    {
        $activities = Activity::query()->where(
            ['log_name' => ActivityConstant::PACKAGE_LOG, 'subject_id' => $packageId]
        )->get();
        return resSuccessWithinData(new ListResource($activities, ActivityPackageResource::class));
    }

    public function getConsignmentLog(string $consignmentId): JsonResponse
    {
        $activities = Activity::query()->where(
            ['log_name' => ActivityConstant::CONSIGNMENT_LOG, 'object_id' => $consignmentId]
        )->get();
        return resSuccessWithinData(
            $activities->transform(
                function ($item) {
                    return new $this->_resource($item);
                }
            )
        );
    }
}