<?php

// Test if storage link is working
echo "<h1>Storage Link Test</h1>";

// Check if the storage link exists
if (is_link(__DIR__ . '/storage')) {
    echo "<p style='color:green'>✓ Storage link exists.</p>";
} else {
    echo "<p style='color:red'>✗ Storage link does not exist.</p>";
}

// Check if the bike_images directory exists
$bikeImagesPath = __DIR__ . '/storage/bike_images';
if (is_dir($bikeImagesPath)) {
    echo "<p style='color:green'>✓ bike_images directory exists.</p>";
} else {
    echo "<p style='color:red'>✗ bike_images directory does not exist.</p>";
}

// List all files in the bike_images directory if it exists
if (is_dir($bikeImagesPath)) {
    echo "<h2>Files in bike_images directory:</h2>";
    $files = scandir($bikeImagesPath);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>{$file} - " . (file_exists($bikeImagesPath . '/' . $file) ? "Exists" : "Does not exist") . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Cannot list files in bike_images directory.</p>";
}

// Try to get some images from the database
try {
    // Connect to database
    $dbConfig = include __DIR__ . '/../config/database.php';
    $defaultConnection = $dbConfig['default'];
    $connectionConfig = $dbConfig['connections'][$defaultConnection];

    $host = $connectionConfig['host'];
    $database = $connectionConfig['database'];
    $username = $connectionConfig['username'];
    $password = $connectionConfig['password'];

    $db = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get bike images
    $stmt = $db->query("SELECT * FROM bike_images LIMIT 10");
    $bikeImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Images from database:</h2>";
    echo "<ul>";
    foreach ($bikeImages as $image) {
        $imagePath = __DIR__ . '/storage/' . $image['image_path'];
        echo "<li>ID: {$image['id']} - Path: {$image['image_path']} - " .
             (file_exists($imagePath) ? "File exists" : "File does not exist") . "</li>";

        // Display the image if it exists
        if (file_exists($imagePath)) {
            echo "<img src='/storage/{$image['image_path']}' style='max-width: 200px; max-height: 200px;' />";
        }
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color:red'>Database error: " . $e->getMessage() . "</p>";
}
