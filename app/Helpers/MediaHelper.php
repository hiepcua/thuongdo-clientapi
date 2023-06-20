<?php


namespace App\Helpers;


use App\Models\Attachment;

class MediaHelper
{
    /**
     * @param  string|null  $value
     * @return string|null
     */
    public static function getFullUrlByValue(?string $value): ?string
    {
        return $value ? (config('app.media_url').'/storage'.$value) : $value;
    }

    /**
     * @param  string|null  $value
     * @return string
     */
    public static function getDomain(?string $value): string
    {
        return config('app.media_url') . "/api/file/$value";
    }
}