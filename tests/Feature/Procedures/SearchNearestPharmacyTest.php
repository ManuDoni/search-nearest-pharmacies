<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\JsonRpcRequestFactory;
use Tests\TestCase;
use function GuzzleHttp\json_encode;
use App\Facades\PharmaciesRegistry;
use Illuminate\Support\Facades\Storage;

class SearchNearestPharmacyTest extends TestCase
{
    public function test_SearchNearestPharmacy()
    {
        $requestJson = JsonRpcRequestFactory::create(
            'SearchNearestPharmacy',
            [
                'currentLocation' => [
                    'latitude' => 41.10938993,
                    'longitude' => 15.0321010,
                ],
                'range'=> 5000,
            ]
        );
        PharmaciesRegistry::shouldReceive('getRegistry')->once()->andReturn(collect([
            [
                'geometry' => [
                    'coordinates' => [
                        15.0321010,
                        41.10938993,
                    ]
                ]
            ],
            [
                'geometry' => [
                    'coordinates' => [
                        15.0321099,
                        41.10938999,
                    ]
                ]
            ],
            //far away coordinates
            [
                'geometry' => [
                    'coordinates' => [
                        7.0321010,
                        10.10938993,
                    ]
                ]
            ]
        ]));

        $response = $this->callJson('POST', route('rpc.endpoint'), $requestJson);

        $response->assertStatus(200);
        $response->assertExactJson([
            'id' => (string)$requestJson['id'],
            'result' => [
                [
                    'geometry' => [
                        'coordinates' => [
                            15.0321010,
                            41.10938993,
                        ]
                    ]
                ],
                [
                    'geometry' => [
                        'coordinates' => [
                            15.0321099,
                            41.10938999,
                        ]
                    ]
                ],
            ],
            'jsonrpc' => '2.0'
        ]);
    }

    public function test_SearchNearestPharmacy_with_limit()
    {
        $requestJson = JsonRpcRequestFactory::create(
            'SearchNearestPharmacy',
            [
                'currentLocation' => [
                    'latitude' => 41.10938993,
                    'longitude' => 15.0321010,
                ],
                'range'=> 5000,
                'limit' => 1,
            ]
        );
        PharmaciesRegistry::shouldReceive('getRegistry')->once()->andReturn(collect([
            [
                'geometry' => [
                    'coordinates' => [
                        15.0321010,
                        41.10938993,
                    ]
                ]
            ],
            [
                'geometry' => [
                    'coordinates' => [
                        15.0321099,
                        41.10938999,
                    ]
                ]
            ],
            //far away coordinates
            [
                'geometry' => [
                    'coordinates' => [
                        4.12334232,
                        9.12343268,
                    ]
                ]
            ]
        ]));

        $response = $this->callJson('POST', route('rpc.endpoint'), $requestJson);

        $response->assertStatus(200);
        $response->assertExactJson([
            'id' => (string)$requestJson['id'],
            'result' => [
                [
                    'geometry' => [
                        'coordinates' => [
                            15.0321010,
                            41.10938993,
                        ]
                    ]
                ]
            ],
            'jsonrpc' => '2.0'
        ]);
    }


    public function test_SearchNearestPharmacy_that_loads_registry_from_file()
    {
        $requestJson = JsonRpcRequestFactory::create(
            'SearchNearestPharmacy',
            [
                'currentLocation' => [
                    'latitude' => 41.16866648,
                    'longitude' => 15.11105952,
                ],
                'range'=> 5000,
                'limit' => 1,
            ]
        );
        //create a fake disk and save a copy of the registry,
        //then set the configs to read that file
        Storage::fake('test');
        Storage::disk('test')->put('/registry.json', file_get_contents(base_path('/tests/stubs/registry.json')));
        config(['pharmacies.disk' => 'test']);
        config(['pharmacies.origin' => '/registry.json']);

        app()->make(PharmaciesRegistry::class);

        $response = $this->callJson('POST', route('rpc.endpoint'), $requestJson);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => (string)$requestJson['id'],
            'result' => [
                [
                    'geometry' => [
                        'coordinates' => [
                            15.11105952,
                            41.16866648
                        ]
                    ]
                ]
            ],
            'jsonrpc' => '2.0'
        ]);
    }

    protected function callJson($method, $url, $json)
    {
        $json = is_array($json) ? json_encode($json) : $json;
        return $this->call($method, $url, [], [], [], ['Content-Type' => 'application/json'], $json);
    }
}
