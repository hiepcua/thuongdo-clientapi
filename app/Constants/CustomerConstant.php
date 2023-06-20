<?php


namespace App\Constants;


class CustomerConstant
{
    public const CUSTOMER_STATUS = ['Khóa', 'Đang hoạt động'];
    public const CUSTOMER_STATUS_INACTIVE_KEY = 0;

    public const SERVICES = ['Order', 'Ký gửi'];

    public const KEY_REPORT_CONSIGNMENT = 'consignment_number';
    public const KEY_REPORT_PACKAGE = 'packages_number';
    public const KEY_REPORT_ORDER = 'orders_number';
    public const KEY_REPORT_ORDER_COST = 'order_amount';
    public const KEY_REPORT_BALANCE_AMOUNT = 'balance_amount';
    public const KEY_REPORT_PURCHASE_AMOUNT = 'purchase_amount';
    public const KEY_REPORT_DISCOUNT_AMOUNT = 'discount_amount';
    public const KEY_REPORT_WITHDRAWAL_AMOUNT = 'withdrawal_amount';

    public const KEY_WITHDRAWAL_STATUS_PENDING = 'status_pending';
    public const KEY_WITHDRAWAL_STATUS_PROCESSING= 'status_processing';
    public const KEY_WITHDRAWAL_STATUS_DONE = 'status_done';
    public const KEY_WITHDRAWAL_STATUS_FAIL = 'status_fail';
    public const KEY_WITHDRAWAL_STATUS_CANCEL = 'status_cancel';

    public const WITHDRAWAL_STATUSES = [
        self::KEY_WITHDRAWAL_STATUS_PENDING => 'Chờ xử lý',
        self::KEY_WITHDRAWAL_STATUS_PROCESSING => 'Đang xử lý yêu cầu',
        self::KEY_WITHDRAWAL_STATUS_FAIL => 'Chưa chuyển thành công',
        self::KEY_WITHDRAWAL_STATUS_DONE => 'Đã chuyển thành công',
        self::KEY_WITHDRAWAL_STATUS_CANCEL => 'Đã hủy',
    ];

    public const WITHDRAWAL_COLOR = [
        self::KEY_WITHDRAWAL_STATUS_PENDING => ColorConstant::CARROT_ORANGE,
        self::KEY_WITHDRAWAL_STATUS_PROCESSING => ColorConstant::TIFFANY_BLUE,
        self::KEY_WITHDRAWAL_STATUS_FAIL =>  ColorConstant::RED,
        self::KEY_WITHDRAWAL_STATUS_DONE => ColorConstant::GREEN,
        self::KEY_WITHDRAWAL_STATUS_CANCEL => ColorConstant::PRUSSIAN_BLUE,
    ];
}