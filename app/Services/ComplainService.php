<?php


namespace App\Services;


use App\Constants\ComplainConstant;
use App\Helpers\PaginateHelper;
use App\Http\Resources\Complain\ComplainResource;
use App\Http\Resources\ListResource;
use App\Models\Complain;
use App\Models\ComplainImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ComplainService extends BaseService
{
    protected string $_resource = ComplainResource::class;

    /**
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getListByOrderId(string $orderId): JsonResponse
    {
        $complains = Complain::query()->where('order_id', $orderId)->limit(PaginateHelper::getLimit())->get();
        return resSuccessWithinData((new ListResource($complains, $this->_resource)));
    }

    public function insertImages(string $complainId, array $imagesBill, array $imageReceived): void
    {
        $images = [];
        $this->appendImages($images, $imagesBill, $complainId, 1);
        $this->appendImages($images, $imageReceived, $complainId, 0);
        ComplainImage::query()->insert($images);
    }

    private function appendImages(array &$images, array $data, string $complainId, bool $isBill)
    {
        foreach ($data as $image) {
            $images[] = [
                'id' => getUuid(),
                'complain_id' => $complainId,
                'image' => $image,
                'is_bill' => $isBill,
                'created_at' => now()
            ];
        }
    }

    /**
     * @param  string  $orderId
     * @return int
     */
    public function getStatusesDone(string $orderId): int
    {
        return Complain::query()->where(
            ['order_id' => $orderId, 'status' => ComplainConstant::KEY_STATUS_DONE]
        )->count();
    }
}