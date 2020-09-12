#Laravel Nearby Pharmacies

This is a test project.

It's a JSON RPC API that serves a single method on the base endpoint `/api/v1/endpoint`

The method is `SearchNearestPharmacy` and this is an example json payload:
```
{
    "jsonrpc": "2.0",
    "method": "SearchNearestPharmacy",
    "params": {
        "currentLocation": {
            "latitude": 40.87838031 ,
            "longitude": 15.02678871
        },
        "range": 50000,
        "limit": 5
    },
    "id": 1
}
```

I used the package `sajya/server` to manage JSON RPC methods.

This repo is tested, I created a class \App\Http\Handlers\PharmaciesRgistryHandler to wrap the json source of the pharmacies registry. This makes it testable using the Facade `\App\Facades\PharmaciesRegistry` because the wrapper can be swapped at runtime dring tests with a mocked one.

The pharmacies registry can be a remote or a local resource, you can set it on the `.env` file.
Keys of `.env` files are:

- `PHARMACIES_REGISTRY_ORIGIN`: the url or path of the json file;
- `PHARMACIES_REGISTRY_DISK`: the disk name of laravel storage (local, S3 ecc.). If the origin is a path and disk is null it will be used the default laravel disk.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
