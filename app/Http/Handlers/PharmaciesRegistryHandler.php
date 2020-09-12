<?php

namespace App\Http\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use function GuzzleHttp\json_decode;

class PharmaciesRegistryHandler
{

    public const CACHE_KEY = 'pharmacies_registry.json';

    /**
     * The path or url used to retrieve the registry
     *
     * @var string
     */
    public string $origin;

    /**
     * The storage disk used to retrieve the registry,
     * if the origin is a path instead of an URL
     *
     * @var string
     */
    public string $storageDisk;

    /**
     * The content of the retrieved registry
     */
    public ?string $content = null;

    public function __construct(string $origin, string $storageDisk = null)
    {
        $this->origin = $origin;
        $this->storageDisk = $storageDisk;
    }

    /**
     * Retrieve registry content and return it as an object
     *
     * @param bool $useCache
     *
     * @return object
     */
    public function getRegistry(bool $useCache = false): Collection
    {
        if ($useCache && Cache::has(static::CACHE_KEY)) {
            return Cache::get(static::CACHE_KEY);
        }
        if (filter_var($this->origin, FILTER_VALIDATE_URL) !== false) {
            $registry = Http::get($this->origin)->json()['features'];
        } else {
            $registry = json_decode(Storage::disk($this->storageDisk)->get($this->origin), true)['features'];
        }
        $registry = collect($registry);
        if ($useCache) {
            Cache::put('pharmacies.json', $registry, now()->addDay());
        }

        return $registry;
    }
}