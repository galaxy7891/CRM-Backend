<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     * 
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'industry' => $this->faker->randomElement(['Education', 'Shipping', 'Human Development']),
            'email' => $this->faker->unique()->safeEmail(),
            'status' => $this->faker->randomElement(['hot', 'warm', 'cold']),
            'phone' => $this->faker->phoneNumber(),
            'owner' => User::factory(),
            'website' => $this->faker->domainName(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'subdistrict' => $this->faker->randomElement(['Semarang Tengah', 'Semarang Barang', 'Genuk', 'Lebaksiu', 'Tembalang', 'Slawi']),
            'village' => $this->faker->randomElement([
                'Pendrikan Kidul',
                'Cihampelas',
                'Cikole',
                'Cidadap',
                'Tegalandong'
            ]),
            'zip_code' => $this->faker->postcode(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ];
    }
}
