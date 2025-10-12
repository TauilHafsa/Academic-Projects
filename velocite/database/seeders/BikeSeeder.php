<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\BikeCategory;
use App\Models\BikeImage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create partner users
        $partnerUsers = [];
        for ($i = 1; $i <= 3; $i++) {
            $partnerUsers[] = User::create([
                'name' => "Partner User $i",
                'email' => "partner$i@velocite.com",
                'password' => bcrypt('password'),
                'role' => 'partner',
                'email_verified_at' => now(),
            ]);
        }

        // Get all bike categories
        $categories = BikeCategory::all();

        // Sample bike names
        $bikeNames = [
            'Road' => [
                'Speedy Roadmaster',
                'Urban Traveler',
                'City Explorer Pro',
                'Road Runner X3'
            ],
            'Mountain' => [
                'Mountain Crusher X1',
                'Trail Blazer Pro',
                'Offroad Beast',
                'Mountain King Deluxe'
            ],
            'City' => [
                'City Commuter',
                'Urban Glider',
                'Metro Rider',
                'City Cruiser Premium'
            ],
            'Electric' => [
                'E-Rider Pro',
                'Electric Cruiser',
                'Power Pedal X',
                'E-Bike Deluxe'
            ],
            'Hybrid' => [
                'All Terrain Hybrid',
                'Multi-Surface Rider',
                'Hybrid Sportster',
                'Urban-Trail Mix'
            ]
        ];

        // Sample descriptions
        $descriptions = [
            "This bike is perfect for city commuting with its comfortable riding position and reliable components. Featuring quality brakes and smooth-shifting gears, it's designed for daily use on urban roads.",
            "An excellent choice for weekend adventures and daily commutes. This bike combines durability with comfort, offering a smooth ride on various surfaces. The lightweight frame makes it easy to handle.",
            "Designed for performance and reliability, this bike will take you anywhere you need to go. With premium components and a sturdy build, it's perfect for regular riders looking for quality.",
            "A versatile bike suited for various terrains. Whether you're riding through city streets or country paths, this bike delivers a comfortable and stable ride experience."
        ];

        // Sample locations
        $locations = [
            'Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Toulouse', 'Nice', 'Strasbourg'
        ];

        // Create sample bikes
        foreach ($partnerUsers as $owner) {
            // Each owner gets 5-8 bikes
            $numBikes = rand(5, 8);

            for ($i = 0; $i < $numBikes; $i++) {
                // Get a random category
                $category = $categories->random();
                $categoryName = explode(' ', $category->name)[0]; // Get first word of category
                $categoryName = isset($bikeNames[$categoryName]) ? $categoryName : 'City'; // Fallback

                // Choose bike name from appropriate category, or fallback to City
                $bikeName = $bikeNames[$categoryName][array_rand($bikeNames[$categoryName])];
                $bikeName .= ' ' . chr(rand(65, 90)) . rand(100, 999); // Add unique identifier

                // Determine if bike is electric
                $isElectric = (strpos($category->name, 'Electric') !== false) || rand(0, 5) === 0;

                // Create bike
                $bike = Bike::create([
                    'owner_id' => $owner->id,
                    'category_id' => $category->id,
                    'title' => $bikeName,
                    'description' => $descriptions[array_rand($descriptions)],
                    'brand' => $this->getRandomBrand(),
                    'model' => 'Model ' . chr(rand(65, 90)) . '-' . rand(10, 99),
                    'year' => rand(2018, 2023),
                    'color' => $this->getRandomColor(),
                    'frame_size' => $this->getRandomFrameSize(),
                    'condition' => $this->getRandomCondition(),
                    'hourly_rate' => rand(5, 15),
                    'daily_rate' => rand(20, 50),
                    'weekly_rate' => rand(100, 250),
                    'security_deposit' => rand(50, 200),
                    'location' => $locations[array_rand($locations)],
                    'latitude' => 48.8566 + (rand(-100, 100) / 1000), // Random near Paris
                    'longitude' => 2.3522 + (rand(-100, 100) / 1000), // Random near Paris
                    'is_electric' => $isElectric,
                    'is_available' => true,
                    'average_rating' => rand(3, 5) + (rand(0, 10) / 10),
                    'rating_count' => rand(0, 20),
                ]);

                // Add dummy image placeholders
                BikeImage::create([
                    'bike_id' => $bike->id,
                    'image_path' => 'bikes/placeholder.jpg',
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);

                // Add additional images
                for ($j = 1; $j < rand(1, 4); $j++) {
                    BikeImage::create([
                        'bike_id' => $bike->id,
                        'image_path' => 'bikes/placeholder.jpg',
                        'is_primary' => false,
                        'sort_order' => $j,
                    ]);
                }
            }
        }
    }

    private function getRandomBrand()
    {
        $brands = ['Trek', 'Specialized', 'Giant', 'Cannondale', 'Scott', 'Bianchi', 'Cube', 'Merida', 'BMC', 'Orbea'];
        return $brands[array_rand($brands)];
    }

    private function getRandomColor()
    {
        $colors = ['Black', 'White', 'Red', 'Blue', 'Green', 'Yellow', 'Silver', 'Grey', 'Orange', 'Purple'];
        return $colors[array_rand($colors)];
    }

    private function getRandomFrameSize()
    {
        $sizes = ['S', 'M', 'L', 'XL', '48cm', '52cm', '54cm', '56cm', '58cm', '60cm'];
        return $sizes[array_rand($sizes)];
    }

    private function getRandomCondition()
    {
        $conditions = ['new', 'like_new', 'good', 'fair'];
        return $conditions[array_rand($conditions)];
    }
}
