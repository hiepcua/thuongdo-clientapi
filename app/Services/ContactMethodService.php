<?php


namespace App\Services;


use App\Models\ContactMethod;
use App\Models\Supplier;
use Illuminate\Support\Str;

class ContactMethodService extends BaseService
{
    public function insertFromSupplier(Supplier $supplier, array $orderDetail)
    {
        foreach ($orderDetail as $key => $item) {
            removeKeyNotExistsModel($item, (new ContactMethod())->getFillable());
            $item['supplier_id'] = $supplier->id;
            $orderDetail[$key] = $item;
        }
        ContactMethod::query()->insert($orderDetail);
    }
}
