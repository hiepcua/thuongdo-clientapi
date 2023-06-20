<?php


namespace App\Helpers;


class StringHelper
{
    public static function convertArrayToString(array $array, ?string $separator = ','): string
    {
        return implode($separator, $array);
    }
}