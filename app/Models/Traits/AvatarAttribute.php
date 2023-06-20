<?php


namespace App\Models\Traits;


use App\Helpers\MediaHelper;

trait AvatarAttribute
{
    /**
     * @param  string|null  $value
     * @return string
     */
    public function getAvatarAttribute(?string $value): ?string
    {
        return MediaHelper::getFullUrlByValue($value);
    }
}