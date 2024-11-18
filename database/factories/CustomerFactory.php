<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Customer::class;
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'id' => $this->faker->uuid(),
            'customers_company_id' => null,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'customerCategory' => Arr::random(['leads', 'contact']),
            'job' => Arr::random(['PNS', 'Wiraswasta', 'Karyawan Swasta', 'Freelancer']),
            'description' => $this->faker->sentence(),
            'status' => Arr::random(['warm', 'cold', 'hot']),
            'email' => strtolower($firstName . '.' . $lastName) . '@example.com',
            'phone' => $this->faker->unique()->numerify('62########'),
            'owner' => 'user_satu@gmail.com',

        ];
    }
}
