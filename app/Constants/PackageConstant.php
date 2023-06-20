<?php


namespace App\Constants;


class PackageConstant
{
    public const INDEX_STATUS_WAITING_CODE = 14;
    public const STATUS_PENDING = 'status_0';
    public const STATUS_IN_PROGRESS = 'status_15';
    public const STATUS_WAREHOUSE_VN = 'status_10';
    public const STATUS_WAITING_CODE = 'status_14';
    public const STATUS_DELIVERED = 'status_16';
    public const STATUS_CANCEL = 'status_17';
    public const STATUSES = [
        self::STATUS_PENDING => 'Chờ xử lý',
        'status_1' => 'Xác nhận lại',
        'status_2' => 'Đang ở kho QC',
        'status_3' => 'Đang về Bằng Tường',
        'status_4' => 'Đang về kho TQ',
        'status_5' => 'Đã đến kho TQ',
        'status_6' => 'Đang về VN',
        'status_7' => 'Đến kho Bằng Tường',
        'status_8' => 'Đang kiểm hàng',
        'status_9' => 'Đang chuyển hàng',
        self::STATUS_WAREHOUSE_VN => 'Đến kho VN',
        'status_11' => 'Đang về Tp.HCM',
        'status_12' => 'Đang kiểm hàng Tp.HCM',
        'status_13' => 'Đang về Hải Phòng',
        self::STATUS_WAITING_CODE => 'Chờ COD',
        self::STATUS_IN_PROGRESS => 'Đang giao hàng',
        self::STATUS_DELIVERED => 'Đã nhận hàng',
        self::STATUS_CANCEL => 'Đã hủy',
    ];

    public const STATUSES_COLOR = [
        ColorConstant::STRAWBERRY,
        ColorConstant::CARROT_ORANGE,
        ColorConstant::APPLE,
        ColorConstant::NAVY_BLUE,
        ColorConstant::TIFFANY_BLUE,
        ColorConstant::RED,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
        ColorConstant::PRUSSIAN_BLUE,
    ];

    public const STATUES_SHOW_DETAILS = [
        self::STATUS_PENDING,
        'status_4',
        'status_5',
        'status_6',
        self::STATUS_WAREHOUSE_VN,
        self::STATUS_DELIVERED
    ];
}