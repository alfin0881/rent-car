<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                'brand' => 'Toyota',
                'model' => 'Avanza',
                'plate_number' => 'B 1234 XYZ',
                'year' => 2022,
                'price_per_day' => 300000,
                'status' => 'available',
            ],
            [
                'brand' => 'Toyota',
                'model' => 'Fortuner',
                'plate_number' => 'B 1122 STU',
                'year' => 2023,
                'price_per_day' => 600000,
                'status' => 'available',
            ],
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}