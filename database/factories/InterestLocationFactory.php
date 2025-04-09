<?php

namespace Database\Factories;

use App\Models\TypeLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InterestLocation>
 */
class InterestLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'il_name' => $this->faker->company,
            'il_scope' => rand(1, 5),
            'il_longlat' => DB::raw("POINT({$this->faker->longitude}, {$this->faker->latitude})"),
            'il_address' => $this->faker->address,
            'il_us_id' => User::inRandomOrder()->first()?->us_id,
            'il_tl_id' => TypeLocation::inRandomOrder()->first()?->tl_id,
            'il_subdistrict' => $this->faker->citySuffix,
            'il_district' => $this->faker->city,
            'il_province' => $this->faker->state,
            'il_postalcode' => $this->faker->postcode,
        ];
    }
}
