<?php

namespace App\Helpers;

use Illuminate\Support\Facades\URL;

class AssetHelper
{
    /**
     * get the url for a cached background image
     *
     * @param string $filename
     * @return string
     */
    public static function cachedBackground($filename)
    {
        return URL::to('/cached-bg/' . $filename);
    }
    
    /**
     * get the url for a cached telegram emoji
     *
     * @param string $filename
     * @return string
     */
    public static function cachedEmoji($filename)
    {
        return URL::to('/cached-emoji/' . $filename);
    }
} 