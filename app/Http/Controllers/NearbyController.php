<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class NearbyController extends Controller
{
    //
    public function index(Request $request, $branchId)
    {
        $branch = Branch::select(
            'br_scope',
            DB::raw('ST_X(br_longlat) as lng'),
            DB::raw('ST_Y(br_longlat) as lat')
        )->findOrFail($branchId);

        $lat = $branch->lat;
        $lng = $branch->lng;
        $radiusInKm = $branch->br_scope;
        $limit = $request->query('limit', 10);

        // Branch Nearby
        $nearbyBranches = DB::table('branch')
            ->whereNull('deleted_at')
            ->where('br_id', '!=', $branchId)
            ->select('*', DB::raw("(
                6371 * acos(
                    cos(radians($lat)) *
                    cos(radians(ST_Y(br_longlat))) *
                    cos(radians(ST_X(br_longlat)) - radians($lng)) +
                    sin(radians($lat)) *
                    sin(radians(ST_Y(br_longlat)))
                )
            ) as distance"))
            ->having('distance', '<=', $radiusInKm)
            ->orderBy('distance')
            ->limit($limit)
            ->get()
            ->map(function ($b) {
                return [
                    'type' => 'branch',
                    'id' => $b->br_id,
                    'name' => $b->br_name,
                    'address' => $b->br_address . ' ' . $b->br_subdistrict . ' ' . $b->br_district . ' ' . $b->br_province,
                    'distance' => round($b->distance, 2),
                    'image' => DB::table('image')->where('i_br_id', $b->br_id)->pluck('i_pathUrl')->first(),
                ];
            });

        // Interest Location Nearby
        $nearbyInterests = DB::table('interest_location')
            ->whereNull('deleted_at')
            ->select('*', DB::raw("(
                6371 * acos(
                    cos(radians($lat)) *
                    cos(radians(ST_Y(il_longlat))) *
                    cos(radians(ST_X(il_longlat)) - radians($lng)) +
                    sin(radians($lat)) *
                    sin(radians(ST_Y(il_longlat)))
                )
            ) as distance"))
            ->having('distance', '<=', $radiusInKm)
            ->orderBy('distance')
            ->limit($limit)
            ->get()
            ->map(function ($i) {
                return [
                    'type' => 'interest',
                    'id' => $i->il_id,
                    'name' => $i->il_name,
                    'address' => $i->il_address . ' ' . $i->il_subdistrict . ' ' . $i->il_district . ' ' . $i->il_province,
                    'distance' => round($i->distance, 2),
                    'image' => DB::table('image')->where('i_il_id', $i->il_id)->pluck('i_pathUrl')->first(),
                ];
            });


        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 5);

        $combinedResults = collect([...$nearbyBranches, ...$nearbyInterests])
            ->sortBy('distance')
            ->values(); // รีเซ็ต index

        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 5);

        $paginatedCombined = new LengthAwarePaginator(
            $combinedResults->forPage($page, $perPage)->values(),
            $combinedResults->count(),
            $perPage,
            $page,
            ['path' => url()->current()]
        );

        return response()->json([
            'branch_id' => $branchId,
            'scope_km' => $radiusInKm,
            'nearby_combined' => $paginatedCombined, // แค่ field เดียว
        ]);
    }
}
