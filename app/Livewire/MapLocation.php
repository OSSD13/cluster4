<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\InterestLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class MapLocation extends Component
{
    use WithFileUploads;
    public $long, $lat;
    public $center = [100.5018, 13.7563]; // Bangkok coordinates
    public $mapBoxToken;
    public $scope = 5;

    public $nearbyPreview = [];

    protected function getListeners(): array
    {
        return [
            'setLongLat' => 'setLongLat',
            'updateLocation' => 'updateLocation',
            'updateScope' => 'updateScope',
        ];
    }

    public function mount()
    {
        $this->mapBoxToken = env('MAPBOX_ACCESS_TOKEN');
    }

    public $branchId, $nameBranch, $codeBranch, $phoneBranch, $addressBranch, $zipcodeBranch, $provinceBranch, $amphoeBranch, $districtBranch, $imageBranch;


    public $geoJsonBranch;

    #[On('generateCodeBranch')]
    public function generateCodeBranch()
    {
        $zipcode = substr($this->zipcodeBranch ?? '', 0, 2);
        $this->codeBranch = $zipcode . '-' . str_pad(Branch::withTrashed()->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function loadBranchLocation()
    {
        $years = Carbon::now()->year;

        $user = auth()->user();
        $locations = Branch::query()
            ->when($user && $user->us_role === 'Sales', function ($query) use ($user) {
                $query->where('br_us_id', $user->us_id);
            })
            ->when($user && $user->us_role === 'Sales Supervisor', function ($query) use ($user) {
                $query->whereIn('br_us_id', function ($sub) use ($user) {
                    $sub->select('us_id')
                        ->from('users')
                        ->where('us_head', $user->us_id)
                        ->orWhere('us_id', $user->us_id);
                });
            })
            ->with([
                'image',
                'manager',
                'order'
            ])
            ->select(
                'br_id',
                'br_code',
                'br_name',
                'br_phone',
                'br_scope',
                DB::raw('ST_Y(br_longlat) as lat'),
                DB::raw('ST_X(br_longlat) as lng'),
                'br_address',
                'br_subdistrict',
                'br_district',
                'br_province',
                'br_postalcode',
                'br_us_id',
            )
            ->get();

        $arrayLocation = [];
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม',
        ];
        foreach ($locations as $location) {
            $address = $location->br_address . ' ' . $location->br_subdistrict . ' ' . $location->br_district . ' ' . $location->br_province . ' ' . $location->br_postalcode;
            $images = $location->image->pluck('i_pathUrl');
            $manager = $location->manager?->us_fname . ' ' . $location->manager?->us_lname;

            $monthlySales = collect(range(1, 12))->map(function ($monthNum) use ($location, $thaiMonths, $years) {
                $orders = $location->order->filter(function ($order) use ($monthNum, $years) {
                    $isSameYear = Carbon::parse($order->created_at)->year === $years;
                    $isSameMonth = (int)Carbon::parse($order->created_at)->format('n') === $monthNum;
                    return $isSameYear && $isSameMonth;
                });

                $latestOrder = $orders->sortByDesc(function ($o) {
                    return $o->updated_at !== null && $o->updated_at != $o->created_at
                        ? $o->updated_at
                        : $o->created_at;
                })->first();

                return [
                    'month' => $thaiMonths[$monthNum],
                    'amount' => $latestOrder->od_amount ?? 0,
                    'order_count' => $orders->count(),
                ];
            });

            $arrayLocation[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$location->lng, (float)$location->lat],
                ],
                'properties' => [
                    'id' => $location->br_id,
                    'code' => $location->br_code,
                    'name' => $location->br_name,
                    'address' => $address,
                    'scope' => (float)$location->br_scope,
                    'image' => $images->values()->toArray(),
                    'phone' => $location->br_phone,
                    'manager_id' => $location->manager?->us_id,
                    'manager_name' => $manager,
                    'manager_image' => $location->manager?->us_image,
                    'manager_role' => $location->manager?->us_role,
                    'manager_email' => $location->manager?->us_email,
                    'orders' => $monthlySales->toArray(),
                ],
            ];
        }

        $geoBranch = [
            'type' => 'FeatureCollection',
            'features' => $arrayLocation,
        ];

        $geoJson = collect($geoBranch)->toJson();
        $this->geoJsonBranch = $geoJson;
    }

    public function validateBranchForm()
    {
        $validate = $this->validate([
            'nameBranch' => 'required|unique:branch,br_name',
            'codeBranch' => 'required|unique:branch,br_code',
            'phoneBranch' => 'required|min:10',
            'addressBranch' => 'required',
            'zipcodeBranch' => 'required|numeric',
            'provinceBranch' => 'required',
            'amphoeBranch' => 'required',
            'districtBranch' => 'required',
            'imageBranch.*' => 'image|max:2048',
        ], [
            'nameBranch.required' => 'กรุณากรอกชื่อสาขา',
            'nameBranch.unique' => 'ชื่อสาขานี้มีอยู่ในระบบแล้ว',
            'codeBranch.required' => 'กรุณากรอกรหัสสาขา',
            'codeBranch.unique' => 'รหัสสาขานี้มีอยู่ในระบบแล้ว',
            'phoneBranch.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'phoneBranch.min' => 'กรุณากรอกเบอร์โทรศัพท์ให้ครบ',
            'addressBranch.required' => 'กรุณากรอกบ้านเลขที่',
            'zipcodeBranch.required' => 'กรุณากรอกรหัสไปรษณีย์',
            'provinceBranch.required' => 'กรุณากรอกจังหวัด',
            'amphoeBranch.required' => 'กรุณากรอกอำเภอ',
            'districtBranch.required' => 'กรุณากรอกตำบล',
            'imageBranch.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
            'imageBranch.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2MB',
            'imageBranch.*.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
        ]);

        $this->dispatch('showConfirmCreateBranch');
    }

    public function validateEditBranchForm()
    {
        $validate = $this->validate([
            'nameBranch' => 'required',
            'codeBranch' => 'required',
            'phoneBranch' => 'required|min:10',
            'addressBranch' => 'required',
            'zipcodeBranch' => 'required|numeric',
            'provinceBranch' => 'required',
            'amphoeBranch' => 'required',
            'districtBranch' => 'required',
            'imageBranch.*' => 'image|max:2048',
        ], [
            'nameBranch.required' => 'กรุณากรอกชื่อสาขา',
            'nameBranch.unique' => 'ชื่อสาขานี้มีอยู่ในระบบแล้ว',
            'codeBranch.required' => 'กรุณากรอกรหัสสาขา',
            'codeBranch.unique' => 'รหัสสาขานี้มีอยู่ในระบบแล้ว',
            'phoneBranch.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'phoneBranch.min' => 'กรุณากรอกเบอร์โทรศัพท์ให้ครบ',
            'addressBranch.required' => 'กรุณากรอกบ้านเลขที่',
            'zipcodeBranch.required' => 'กรุณากรอกรหัสไปรษณีย์',
            'provinceBranch.required' => 'กรุณากรอกจังหวัด',
            'amphoeBranch.required' => 'กรุณากรอกอำเภอ',
            'districtBranch.required' => 'กรุณากรอกตำบล',
            'imageBranch.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
            'imageBranch.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2MB',
            'imageBranch.*.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
        ]);

        $this->dispatch('showConfirmEditBranch');
    }

    #[On('create-branch')]
    public function createBranch()
    {
        try {
            DB::transaction(function () {
                do {
                    $this->generateCodeBranch();
                } while (
                    Branch::where('br_code', $this->codeBranch)->lockForUpdate()->exists()
                );

                $branch = Branch::create([
                    'br_code' => $this->codeBranch,
                    'br_name' => $this->nameBranch,
                    'br_phone' => $this->phoneBranch,
                    'br_scope' => $this->scope,
                    'br_longlat' => DB::raw("ST_GeomFromText('POINT($this->long $this->lat)', 4326)"),
                    'br_address' => $this->addressBranch,
                    'br_subdistrict' => $this->amphoeBranch,
                    'br_district' => $this->districtBranch,
                    'br_province' => $this->provinceBranch,
                    'br_postalcode' => $this->zipcodeBranch,
                    'br_us_id' => auth()->id(),
                ]);

                if ($this->imageBranch) {
                    foreach ($this->imageBranch as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('images_branch', $imageName, 'public');
                        $imageUrl = Storage::url('/app/public/images_branch/' . $imageName);
                        $branch->image()->create([
                            'i_pathUrl' => $imageUrl,
                        ]);
                    }
                }
            });

            $this->reset(['codeBranch', 'nameBranch', 'phoneBranch', 'addressBranch', 'zipcodeBranch', 'provinceBranch', 'amphoeBranch', 'districtBranch', 'imageBranch']);
            $this->dispatch('branch-added-alert');
            $this->loadBranchLocation();
            $this->dispatch('updateBranchLocation', $this->geoJsonBranch);
        } catch (\Exception $e) {
            // dd($e);
            $this->dispatch('error', 'เกิดข้อผิดพลาดในการสร้างสาขา กรุณาลองใหม่อีกครั้ง');
            return;
        }
    }

    #[On('delete-branch')]
    public function deleteBranch($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $branch = Branch::findOrFail($id);
                $branch->image()->delete();
                $branch->delete();
            });
            $this->loadBranchLocation();
            $this->dispatch('deleteBranchLocation', $this->geoJsonBranch);
            $this->dispatch('branch-deleted-alert');
        } catch (\Exception $e) {
            // dd($e);
            $this->dispatch('error', 'เกิดข้อผิดพลาดในการลบสาขา กรุณาลองใหม่อีกครั้ง');
            return;
        }
    }

    #[On('edit-branch')]
    public function editBranch($id)
    {
        $branchData = Branch::select(
            '*',
            DB::raw('ST_Y(br_longlat) as lat'),
            DB::raw('ST_X(br_longlat) as lng')
        )->findOrFail($id);

        $this->branchId = $branchData->br_id;
        $this->nameBranch = $branchData->br_name;
        $this->codeBranch = $branchData->br_code;
        $this->phoneBranch = $branchData->br_phone;
        $this->addressBranch = $branchData->br_address;
        $this->zipcodeBranch = $branchData->br_postalcode;
        $this->provinceBranch = $branchData->br_province;
        $this->amphoeBranch = $branchData->br_subdistrict;
        $this->districtBranch = $branchData->br_district;
        $this->scope = $branchData->br_scope;
        $this->lat = $branchData->lat;
        $this->long = $branchData->lng;
    }


    #[On('save-edit-branch')]
    public function saveEditBranch()
    {
        try {
            DB::transaction(function () {
                $branch = Branch::findOrFail($this->branchId);

                $branch->update([
                    'br_code' => $this->codeBranch,
                    'br_name' => $this->nameBranch,
                    'br_phone' => $this->phoneBranch,
                    'br_scope' => $this->scope,
                    'br_longlat' => DB::raw("ST_GeomFromText('POINT($this->long $this->lat)', 4326)"),
                    'br_address' => $this->addressBranch,
                    'br_subdistrict' => $this->amphoeBranch,
                    'br_district' => $this->districtBranch,
                    'br_province' => $this->provinceBranch,
                    'br_postalcode' => $this->zipcodeBranch,
                    'br_us_id' => auth()->id(),
                ]);

                if ($this->imageBranch) {
                    foreach ($this->imageBranch as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('images_branch', $imageName, 'public');
                        $imageUrl = Storage::url('/app/public/images_branch/' . $imageName);
                        $branch->image()->create([
                            'i_pathUrl' => $imageUrl,
                        ]);
                    }
                }
            });

            $this->reset(['branchId', 'codeBranch', 'nameBranch', 'phoneBranch', 'addressBranch', 'zipcodeBranch', 'provinceBranch', 'amphoeBranch', 'districtBranch', 'imageBranch']);
            $this->dispatch('branch-edit-alert');
            $this->loadBranchLocation();
            $this->dispatch('updateEditBranchLocation', $this->geoJsonBranch);
        } catch (\Exception $e) {
            // dd($e);
            $this->dispatch('error', 'เกิดข้อผิดพลาดในการสร้างสาขา กรุณาลองใหม่อีกครั้ง');
            return;
        }
    }

    public function loadNearbyPreview()
    {
        if (!$this->long || !$this->lat) return;

        $radius = $this->scope ?? 5; // กิโลเมตร
        $radiusMeters = $radius * 1000;

        $branches = Branch::selectRaw('*, ST_Distance_Sphere(POINT(?, ?), br_longlat) as distance', [$this->long, $this->lat])
            ->whereRaw("ST_Distance_Sphere(POINT(?, ?), br_longlat) <= ?", [$this->long, $this->lat, $radiusMeters])
            ->whereNull('deleted_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'branch',
                    'name' => $item->br_name,
                    'address' => $item->br_address,
                    'distance' => round($item->distance / 1000, 2),
                ];
            });

        $interests = InterestLocation::selectRaw('*, ST_Distance_Sphere(POINT(?, ?), il_longlat) as distance', [$this->long, $this->lat])
            ->whereRaw("ST_Distance_Sphere(POINT(?, ?), il_longlat) <= ?", [$this->long, $this->lat, $radiusMeters])
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'interest',
                    'name' => $item->il_name,
                    'address' => $item->il_address,
                    'distance' => round($item->distance / 1000, 2),
                ];
            });

        $this->nearbyPreview = collect($branches)
            ->merge($interests)
            ->sortBy('distance')
            ->take(5)
            ->values()
            ->toArray();
    }


    #[On('clearEditBranchForm')]
    public function clearEditBranch()
    {
        $this->reset(['codeBranch', 'nameBranch', 'phoneBranch', 'addressBranch', 'zipcodeBranch', 'provinceBranch', 'amphoeBranch', 'districtBranch', 'imageBranch']);
    }


    #[On('updateZipBranch')]
    public function setZipcodeBranch($zipcode = null)
    {
        $this->zipcodeBranch = $zipcode;
    }

    #[On('updateProBranch')]
    public function setProvinceBranch($province = null)
    {
        $this->provinceBranch = $province;
    }

    #[On('updateAmpBranch')]
    public function setAmphoeBranch($amphoe = null)
    {
        $this->amphoeBranch = $amphoe;
    }

    #[On('updateDisBranch')]
    public function setDistrictBranch($district = null)
    {
        $this->districtBranch = $district;
    }

    public function updateLocation($long = null, $lat = null)
    {
        $this->center = [$long, $lat];
        $this->dispatch('locationUpdate', $this->center);
    }

    public function updateScope($scope)
    {
        $this->scope = $scope;
        $this->loadNearbyPreview();
    }

    public function setLongLat($long, $lat)
    {
        $this->long = $long;
        $this->lat = $lat;
        $this->loadNearbyPreview();
    }

    public function render()
    {
        $this->loadBranchLocation();
        return view('livewire.map-location');
    }
}
