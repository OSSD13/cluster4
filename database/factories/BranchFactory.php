<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
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
            'br_code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'br_name' => $this->faker->company,
            'br_phone' => $this->faker->phoneNumber,
            'br_scope' => rand(1, 10),
            'br_longlat' => DB::raw("POINT({$this->faker->longitude}, {$this->faker->latitude})"),
            'br_address' => $this->faker->address,
            'br_subdistrict' => $this->faker->citySuffix,
            'br_district' => $this->faker->city,
            'br_province' => $this->faker->state,
            'br_postalcode' => $this->faker->postcode,
            'br_us_id' => User::inRandomOrder()->first()?->us_id,
        ];
    }
}
