<?php

namespace Database\Factories;

use App\Models\Owner;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => null,
            'qty_people' => random_int(1, 10),
            'has_geo_board' => $this->faker->boolean(),
            'has_cams' => $this->faker->boolean(),
            'has_phone_signal' => $this->faker->boolean(),
            'has_internet' => $this->faker->boolean(),
            'has_gun' => $this->faker->boolean(),
            'has_gun_local' => $this->faker->boolean(),
            'gun_local_description' => $this->faker->text(),
            'qty_agricultural_defensives' => random_int(0, 10),
            'observations' => $this->faker->text(),
            'fk_owner_id' => new ObjectId(Owner::all()->random()->_id),
            'fk_property_type_id' => new ObjectId(PropertyType::all()->random()->_id)
        ];
    }
}
