<?php

declare(strict_types=1);

namespace App\Http\Procedures;

use App\Facades\PharmaciesRegistry;
use Illuminate\Http\Request;
use Sajya\Server\Procedure;

class SearchNearestPharmacy extends Procedure
{
    /**
     * The name of the procedure that will be
     * displayed and taken into account in the search
     *
     * @var string
     */
    public static string $name = 'SearchNearestPharmacy';

    /**
     * Execute the procedure.
     *
     * @param Request $request
     *
     * @return array|string|integer
     */
    public function SearchNearestPharmacy(Request $request)
    {
        $request->validate([
            'currentLocation.latitude' => 'required|numeric',
            'currentLocation.longitude' => 'required|numeric',
            'range' => 'required|integer',
            'limit' => 'nullable|integer',
        ]);

        $lat = $request->input('currentLocation.latitude');
        $long = $request->input('currentLocation.longitude');
        $range = $request->input('range');
        $limit = $request->input('limit');

        $pharmacies = PharmaciesRegistry::getRegistry(true); //uses cache to load registry
        $i = 0;
        $nearest = $pharmacies->filter(function ($f) use ($lat, $long, $range, $limit, &$i) {
            if ($limit && $i >= $limit) {
                return;
            }
            //calc the distance between two points, convert it in meters and if it's less tan the range then include it in the results
            //I don't know why but in the json the latitude comes after the longitude
            if ($this->distance($f['geometry']['coordinates'][1], $f['geometry']['coordinates'][0], $lat, $long, 'K') * 1000 < $range) {
                $i ++;
                return true;
            }
            return false;
        });


        return $nearest;
    }

    /**
     * Get the distance between two geospatial points
     *
     * @link https://www.geodatasource.com/developers/php
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @param string $unit
     *
     * @return float
     */
    protected function distance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }
}
