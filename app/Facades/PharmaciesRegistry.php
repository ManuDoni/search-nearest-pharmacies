<?php

namespace App\Facades;

use App\Http\Handlers\PharmaciesRegistryHandler;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection getRegistry(bool $useCache)
 * @method static \Mockery\Expectation shouldReceive()
 */
class PharmaciesRegistry extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PharmaciesRegistryHandler::class;
    }
}