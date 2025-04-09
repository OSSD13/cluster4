<?php

namespace App\Http\Controllers;

use App\Models\InterestLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InterestLocationController extends Controller
{
    //
    public function getNearby(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 10000; // หน่วยเป็นเมตร

        if (!$lat || !$lng) return response()->json(['error' => 'Missing lat/lng'], 400);

        $locations = InterestLocation::selectRaw('*, ST_X(il_longlat) as lng, ST_Y(il_longlat) as lat, ST_Distance_Sphere(il_longlat, POINT(?, ?)) as distance', [$lng, $lat])
            ->with(['typeLocation', 'image'])
            ->whereRaw('ST_Distance_Sphere(il_longlat, POINT(?, ?)) < ?', [$lng, $lat, $radius])
            ->limit(1000)
            ->get()
            ->map(function ($loc) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float) $loc->lng, (float) $loc->lat],
                    ],
                    'properties' => [
                        'id' => $loc->il_id,
                        'name' => $loc->il_name,
                        'address' => $loc->il_address,
                        'typeId' => $loc->typeLocation->tl_id,
                        'typeName' => $loc->typeLocation->tl_name,
                        'color' => Str::start($loc->typeLocation->tl_color, '#'),
                        'icon' => $loc->typeLocation->tl_emoji,
                        'scope' => $loc->il_scope,
                        'image' => $loc->image->pluck('i_pathUrl')->toArray(),
                    ]
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $locations,
        ]);
    }
}
