<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Helpers\AssetHelper;

class CachedAsset extends Facade
{
    /**
     * get the registered name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cached-asset';
    }
} 