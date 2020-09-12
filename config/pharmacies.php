<?php

return [

    /**
     * The origin of the pharmacies registry json, it can be a path or an URL.
     */
    'origin' => env('PHARMACIES_REGISTRY_ORIGIN'),

    /**
     * The storage disk used to retrieve the pharmacies registry json if "origin" is a path.
     */
    'disk' => env('PHARMACIES_REGISTRY_DISK'),

];