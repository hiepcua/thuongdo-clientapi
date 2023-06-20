<?php


namespace App\Constants;


class DeliveryConstant
{
    public const PAYMENT_E_WALLET = 'e-wallet';
    public const PAYMENTS = [
        'cod' => 'COD',
        self::PAYMENT_E_WALLET => 'Ví điện tử'
    ];

    public const STATUS_DONE_INDEX = 3;
    public const KEY_STATUS_DONE = 'status_3';
    public const KEY_STATUS_PENDING = 'status_0';

    public const STATUSES = [
        self::KEY_STATUS_PENDING => 'Chờ xử lý',
        'status_1' => 'Đang xử lý',
        'status_2' => 'Đã xử lý',
        self::KEY_STATUS_DONE => 'Đã hoàn thành',
        'status_4' => 'Đã hủy'
    ];

    public const STATUSES_COLOR = [
        ColorConstant::STRAWBERRY,
        ColorConstant::CARROT_ORANGE,
        ColorConstant::APPLE,
        ColorConstant::NAVY_BLUE,
        ColorConstant::TIFFANY_BLUE
    ];
}