<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BikeCategory;

class BikeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Road Bike',
                'description' => 'Lightweight and fast bikes designed for riding on paved roads. Ideal for long distances and speed.',
                'icon' => 'road-bike.svg',
            ],
            [
                'name' => 'Mountain Bike',
                'description' => 'Bikes designed for off-road cycling on rough terrain, featuring durable frames and suspension systems.',
                'icon' => 'mountain-bike.svg',
            ],
            [
                'name' => 'City Bike',
                'description' => 'Comfortable bikes designed for casual riding in urban environments. Perfect for commuting and errands.',
                'icon' => 'city-bike.svg',
            ],
            [
                'name' => 'Electric Bike',
                'description' => 'Bikes with an integrated electric motor for pedal assistance. Great for longer distances with less effort.',
                'icon' => 'electric-bike.svg',
            ],
            [
                'name' => 'Hybrid Bike',
                'description' => 'Versatile bikes that combine features of road and mountain bikes, suitable for various surfaces.',
                'icon' => 'hybrid-bike.svg',
            ],
            [
                'name' => 'Folding Bike',
                'description' => 'Compact bikes that can be folded for easy storage and transport. Perfect for mixed-mode commuting.',
                'icon' => 'folding-bike.svg',
            ],
            [
                'name' => 'Cargo Bike',
                'description' => 'Utility bikes designed to carry heavy loads, featuring extended frames or cargo areas.',
                'icon' => 'cargo-bike.svg',
            ],
            [
                'name' => 'Children\'s Bike',
                'description' => 'Smaller bikes designed specifically for children of various ages.',
                'icon' => 'children-bike.svg',
            ],
            [
                'name' => 'Tandem Bike',
                'description' => 'Bikes designed for two or more riders, with multiple seats and pedal sets.',
                'icon' => 'tandem-bike.svg',
            ],
        ];

        foreach ($categories as $category) {
            BikeCategory::create($category);
        }
    }
}
