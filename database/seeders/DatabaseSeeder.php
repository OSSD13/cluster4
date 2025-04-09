<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Image;
use App\Models\InterestLocation;
use App\Models\Order;
use App\Models\TypeLocation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        TypeLocation::factory()->count(12)->create();
        User::factory()->count(5)->create();
        Branch::factory()->count(20)->create();
        InterestLocation::factory()->count(20)->create();
        Order::factory()->count(50)->create();
        Image::factory()->count(40)->create();
        $this->call([
            UserSeeder::class,
        ]);
    }
}
