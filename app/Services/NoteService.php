<?php


namespace App\Services;


use App\Models\Customer;
use App\Models\OrderDetailNote;
use App\Models\OrderNote;

class NoteService implements Service
{
    public function store(string $model, array $data)
    {
        if(!$data['content']) return;
        return (new $model)::query()->create(
            [
                'order_id' => $data['order_id'] ?? null,
                $data['column'] => $data['id'],
                'subject_id' => getCurrentUser()->id,
                'subject_type' => Customer::class,
                'content' => $data['content'],
                'supplier_id' => $data['supplier_id'] ?? null,
                'is_public' => $data['is_public'] ?? null
            ]
        );
    }

    /**
     * @param  array  $data
     * @return mixed
     */
    public function storeOrderDetailNote(array $data)
    {
        $data['column'] = 'order_detail_id';
        return $this->store(OrderDetailNote::class, $data);
    }

    /**
     * @param  array  $data
     * @return mixed
     */
    public function storeOrderNote(array $data)
    {
        $data['column'] = 'order_id';
        return $this->store(OrderNote::class, $data);
    }

    /**
     * @param  string  $orderId
     * @param  string  $supplierId
     * @param  bool  $isPublic
     * @return mixed
     */
    public function getOrderNote(string $orderId, string $supplierId)
    {
        return optional(
            OrderNote::query()->where(
                [
                    'subject_type' => Customer::class,
                    'supplier_id' => $supplierId,
                    'order_id' => $orderId,
                    'is_public' => true
                ]
            )->orderByDesc('created_at')->first()
        )->content;
    }
}