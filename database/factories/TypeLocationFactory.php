<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeLocation>
 */
class TypeLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $makiIcons = [
            'restaurant',
            'cafe',
            'park',
            'police',
            'hospital',
            'school',
            'fuel',
            'lodging',
            'bank',
            'embassy',
            'fire-station',
            'theatre',
            'toilet',
            'library',
            'pharmacy',
            'post',
            'recycling',
            'religious-christian',
            'religious-muslim',
            'town-hall',
            'zoo'
        ];
        return [
            //
            'tl_name' => $this->faker->word,
            'tl_emoji' => $this->faker->randomElement($makiIcons),
            'tl_color' => $this->faker->safeHexColor,
        ];
    }
}
