<?php


namespace App\Constants;


class OrderConstant
{
    public const STATUS_DEPOSIT_INDEX = 2;
    public const STATUS_ORDERED = 3;
    public const STATUS_CANCELED = 6;
    public const KEY_STATUS_WAITING_QUOTE = 'status_0';
    public const KEY_STATUS_WAITING_DEPOSIT = 'status_1';
    public const KEY_STATUS_DEPOSITED = 'status_2';
    public const KEY_STATUS_CANCEL = 'status_6';
    public const KEY_STATUS_ORDERING= 'status_7';
    public const KEY_STATUS_WAIT_TO_PAY= 'status_8';
    public const STATUSES = [
        self::KEY_STATUS_WAITING_QUOTE => 'Chờ báo giá',
        self::KEY_STATUS_WAITING_DEPOSIT => 'Chờ đặt cọc',
        self::KEY_STATUS_DEPOSITED => 'Đã đặt cọc',
        self::KEY_STATUS_ORDERING => 'Đang đặt hàng',
        self::KEY_STATUS_WAIT_TO_PAY => 'Chờ thanh toán',
        'status_3' => 'Đặt hàng',
        'status_4' => 'Đã hoàn thành',
        'status_5' => 'Cần xác nhận lại',
        self::KEY_STATUS_CANCEL => 'Đã hủy',
    ];

    public const STATUSES_COLOR = [
        ColorConstant::STRAWBERRY,
        ColorConstant::CARROT_ORANGE,
        ColorConstant::APPLE,
        '#0097e6',
        '#8c7ae6',
        ColorConstant::NAVY_BLUE,
        ColorConstant::TIFFANY_BLUE,
        ColorConstant::RED,
        ColorConstant::PRUSSIAN_BLUE,
    ];

    public const DELIVERY_NORMAL = 'normal';
    public const DELIVERY_FAST = 'fast';

    public const DELIVERIES_TEXT = [
        self::DELIVERY_NORMAL => 'Thường',
        self::DELIVERY_FAST => 'Nhanh'
    ];
}