<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'length' => $this->faker->numberBetween(1000, 2000),
            'draft' => $this->faker->numberBetween(100, 900),
            'code' => $this->faker->postcode(),
            'type' => rand(0, 1),
            'payload_type_id' => rand(1,4),
            'status' => rand(0, 1),
        ];
    }
}
