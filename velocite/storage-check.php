<?php

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database credentials - adjust these to match your system
$host = 'localhost';
$database = 'velocite';
$username = 'root';  // Default WAMP username
$password = '';      // Default WAMP password (empty)

try {
    // Connect to the database
    $db = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query bike_images table
    $stmt = $db->query("SELECT * FROM bike_images LIMIT 10");
    $bikeImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($bikeImages) > 0) {
        echo "Found " . count($bikeImages) . " bike images in the database:\n";
        foreach ($bikeImages as $image) {
            echo "ID: {$image['id']} - Bike ID: {$image['bike_id']} - Path: {$image['image_path']}\n";

            // Check if the image file exists
            $imagePath = __DIR__ . '/storage/app/public/' . $image['image_path'];
            $publicPath = __DIR__ . '/public/storage/' . $image['image_path'];

            echo "  Storage app path exists: " . (file_exists($imagePath) ? "Yes" : "No") . "\n";
            echo "  Public path exists: " . (file_exists($publicPath) ? "Yes" : "No") . "\n";
        }
    } else {
        echo "No bike images found in the database.\n";
    }

    // Check if there are any bikes
    $stmt = $db->query("SELECT COUNT(*) as bike_count FROM bikes");
    $bikeCount = $stmt->fetch(PDO::FETCH_ASSOC)['bike_count'];
    echo "\nTotal bikes in database: {$bikeCount}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Helper function to get environment variables
function env($key, $default = null) {
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    return $value;
}
