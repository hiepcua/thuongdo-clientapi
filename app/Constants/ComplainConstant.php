<?php


namespace App\Constants;


class ComplainConstant
{
    public const STATUS_DONE_INDEX = 3;
    public const KEY_STATUS_PENDING = 'status_0';
    public const KEY_STATUS_DONE = 'status_3';
    public const KEY_STATUS_CANCEL = 'status_4';
    public const STATUSES = [
        self::KEY_STATUS_PENDING => 'Chờ xử lý',
        'status_1' => 'Đang xử lý',
        'status_2' => 'Đã xử lý',
        self::KEY_STATUS_DONE => 'Đã hoàn thành',
        self::KEY_STATUS_CANCEL => 'Đã hủy'
    ];
    public const STATUSES_COLOR = [
        ColorConstant::STRAWBERRY,
        ColorConstant::CARROT_ORANGE,
        ColorConstant::APPLE,
        ColorConstant::RED,
        ColorConstant::PRUSSIAN_BLUE
    ];

    public const NOTE_PUBLIC = 'public';
}