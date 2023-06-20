<?php


namespace App\Services;


use App\Models\SupplierBank;
use App\Models\Supplier;
use Illuminate\Support\Str;

class SupplierBankService extends BaseService
{
    public function insertFromSupplier(Supplier $supplier, array $orderDetail)
    {
        foreach ($orderDetail as $key => $item) {
            (new ContactMethodService)->removeKey($item, (new SupplierBank())->getFillable());
            $item['id'] = getUuid();
            $item['supplier_id'] = $supplier->id;
            $orderDetail[$key] = $item;
        }
        SupplierBank::query()->insert($orderDetail);
    }

}
