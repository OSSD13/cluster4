<?php

namespace App\Console\Commands;

use App\Models\InterestLocation;
use App\Models\TypeLocation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportGeoJsonLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-geo-json-locations {files?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import locations from a GeoJSON file into interest_location table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $user = User::first();
        $files = $this->argument('files');

        if (empty($files)) {
            $files = $files = collect(Storage::disk('geojson')->files())
                ->filter(fn($file) => str_ends_with($file, '.geojson'))
                ->values()
                ->toArray();
            if (empty($files)) {
                $this->error('❌ ไม่พบไฟล์ใด ๆ ใน data/');
                return;
            }

            foreach ($files as $f) {
                $this->line("📂 พบไฟล์: {$f}");
            }
        }

        foreach ($files as $filename) {
            if (!Storage::disk('geojson')->exists($filename)) {
                $this->warn("⚠️ ไม่พบไฟล์: {$filename}");
                continue;
            }

            $json = Storage::disk('geojson')->get($filename);
            $data = json_decode($json, true);
            $features = $data['features'] ?? [];

            // 🔍 ดึงชื่อประเภทจากชื่อไฟล์ เช่น 21school.geojson → school
            $basename = basename($filename);
            preg_match('/\d+-\d+-(.+?)\.geojson$/', $basename, $match);
            $typeKey = $match[1] ?? 'unknown';


            $map = [
                // สถานที่ราชการ, สถานศึกษา, สาธารณสุข, สถานที่ทางศาสนา, สถานที่ความมั่นคง, แลนด์มาร์ก, ธนาคาร,โรงแรม, ปั๊มน้ำมัน, ห้างสรรพสินค้า, ธรรมชาติ
                'changwat-center-v2' => 'สถานที่ราชการ',
                'amphoe-center-v2' => 'สถานที่ราชการ',
                'tesaban' => 'สถานที่ราชการ',
                'school' => 'สถานศึกษา',
                'college-and-university' => 'สถานศึกษา',
                'hospital' => 'สาธารณสุข',
                'health-care-station' => 'สาธารณสุข',
                'health-center' => 'สาธารณสุข',
                'church' => 'สถานที่ทางศาสนา',
                'masjid' => 'สถานที่ทางศาสนา',
                'temple' => 'สถานที่ทางศาสนา',
                'samnak-song' => 'สถานที่ทางศาสนา',
                'chedi' => 'สถานที่ทางศาสนา',
                'shine' => 'สถานที่ทางศาสนา',
                'susan' => 'สถานที่ทางศาสนา',
                'pratath' => 'สถานที่ทางศาสนา',
                'police-center' => 'สถานที่ความมั่นคง',
                'police-stop' => 'สถานที่ความมั่นคง',
                'court-center' => 'สถานที่ความมั่นคง',
                'jail' => 'สถานที่ความมั่นคง',
                'building-landmark' => 'แลนด์มาร์ก',
                'bank' => 'ธนาคาร',
                'hotel' => 'โรงแรม',
                'gas-station' => 'ปั๊มน้ำมัน',
                'department-of-lands' => 'ห้างสรรพสินค้า',
                'rural-road-center' => 'สถานที่ราชการ',
                'mountain' => 'ธรรมชาติ',
                'lake' => 'ธรรมชาติ',
                'beach' => 'ธรรมชาติ',
                'waterfall' => 'ธรรมชาติ',
                'hotspring-v2' => 'ธรรมชาติ',
                'cave' => 'ธรรมชาติ',
                'kaeng' => 'ธรรมชาติ',
                'stone-and-hole' => 'ธรรมชาติ',
                'island' => 'ธรรมชาติ',
                'geotour' => 'ธรรมชาติ',
                'view-point' => 'แลนด์มาร์ก',
                'dam' => 'ธรรมชาติ',
                'mining' => 'ธรรมชาติ',
            ];

            $typeDisplayName = $map[$typeKey] ?? 'ไม่ทราบประเภท';

            $type = TypeLocation::where('tl_name', $typeDisplayName)->first();

            if (!$type) {
                $this->warn("⚠️ ไม่พบประเภท '{$typeDisplayName}' ในฐานข้อมูล");
                continue;
            }

            foreach ($features as $f) {
                $props = $f['properties'];
                $coords = $f['geometry']['coordinates'];
                try {
                    InterestLocation::create([
                        'il_name' => $props['name'] ?? $props['NameT'] ?? 'ไม่รู้',
                        'il_longlat' => DB::raw("ST_GeomFromText('POINT({$coords[0]} {$coords[1]})', 4326)"),
                        'il_scope' => 5,
                        'il_tl_id' => $type->tl_id,
                        'il_us_id' => $user->id,
                        'il_address' => '-',
                        'il_subdistrict' => '-',
                        'il_district' => '-',
                        'il_province' => '-',
                        'il_postalcode' => '-',
                    ]);
                } catch (\Exception $e) {
                    $this->warn("⚠️ ไม่สามารถนำเข้าข้อมูล {$props['name']} ได้: " . $e->getMessage());
                }
            }

            $this->info("✅ นำเข้า {$basename} เรียบร้อย จำนวน: " . count($features));
        }

        $this->info('🎉 เสร็จสิ้นทุกไฟล์');
    }
}
