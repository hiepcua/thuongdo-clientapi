<?php


namespace App\Constants;


class LocateConstant
{
    public const COUNTRY_VI = 'vi';
    public const COUNTRY_CN = 'cn';
    public const HANOI = 'hanoi';
    public const HCM = 'hcm';
    public const HP = 'hp';

    public const COUNTRIES = [self::COUNTRY_VI, self::COUNTRY_CN];

    public const HANOI_HCM_HP = [
        self::HANOI => 'Hà Nội',
        self::HCM => 'Hồ Chí Minh',
        self::HP => 'Hải Phòng',
    ];
}