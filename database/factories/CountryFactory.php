<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $countries = [
            'Nigeria' => 'NG', 'United States' => 'US', 'United Kingdom' => 'GB', 'Canada' => 'CA', 'Australia' => 'AU',
            'Germany' => 'DE', 'France' => 'FR', 'Italy' => 'IT', 'Spain' => 'ES', 'Netherlands' => 'NL',
            'South Africa' => 'ZA', 'Kenya' => 'KE', 'Ghana' => 'GH', 'Egypt' => 'EG', 'Morocco' => 'MA',
            'India' => 'IN', 'China' => 'CN', 'Japan' => 'JP', 'South Korea' => 'KR', 'Singapore' => 'SG',
            'Brazil' => 'BR', 'Mexico' => 'MX', 'Argentina' => 'AR', 'Chile' => 'CL', 'Colombia' => 'CO'
        ];

        $countryName = fake()->unique()->randomElement(array_keys($countries));

        return [
            'name' => $countryName,
            'short_code' => $countries[$countryName],
        ];
    }
}
