<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\InterestLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
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
            'i_pathUrl' => $this->faker->imageUrl,
            'i_br_id' => Branch::inRandomOrder()->first()?->br_id,
            'i_il_id' => InterestLocation::inRandomOrder()->first()?->i_l_id,
        ];
    }
}
