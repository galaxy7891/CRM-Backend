<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CustomersCompany;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CustomersCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = CustomersCompany::class;
    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'id' => $this->faker->uuid(),
            'name' => $name,
            'industry' => Arr::random(['Perdagangan', 'Teknologi', 'Pendidikan']),
            'email' => $name . '@example.com',
            'status' => Arr::random(['warm', 'cold', 'hot']),
            'phone' => $this->faker->unique()->numerify('62########'),
            'owner' => 'admin_cdi@gmail.com',
            'description' => $this->faker->sentence(),
        ];
    }
}
