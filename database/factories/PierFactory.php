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
                'length' => mt_rand(1, 200),
                'draft' => mt_rand(1, 200),
                // 'code' => ucwords($this->faker->text(4)) . rand(20,100),
                'code' => $this->faker->countryCode(). '-'.rand(10,100),
                'type' => rand(1, 2),
                'payload_type_id' => rand(1, 4),
                'status' =>1,
        ];
    }
}
