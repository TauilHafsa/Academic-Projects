<?php

echo "Storage Link Check\n";
echo "==================\n\n";

// Check if the storage directory exists in public folder
$publicStoragePath = __DIR__ . '/public/storage';
if (file_exists($publicStoragePath)) {
    echo "✓ Public storage directory exists at: {$publicStoragePath}\n";

    // Check if it's a symbolic link
    if (is_link($publicStoragePath)) {
        echo "✓ Public storage is a symbolic link\n";
        echo "  Target: " . readlink($publicStoragePath) . "\n";
    } else {
        echo "✗ Public storage is NOT a symbolic link\n";
    }
} else {
    echo "✗ Public storage directory doesn't exist\n";
}

// Check storage/app/public directory
$appPublicPath = __DIR__ . '/storage/app/public';
if (is_dir($appPublicPath)) {
    echo "✓ Storage app/public directory exists\n";

    // Check if bike_images directory exists
    $bikeImagesPath = $appPublicPath . '/bike_images';
    if (is_dir($bikeImagesPath)) {
        echo "✓ bike_images directory exists\n";

        // Count and list image files
        $files = scandir($bikeImagesPath);
        $imageFiles = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $bikeImagesPath . '/' . $file;
                if (is_file($filePath)) {
                    $imageFiles[] = $file;
                    echo "  - {$file} (" . filesize($filePath) . " bytes)\n";
                }
            }
        }

        echo "  Found " . count($imageFiles) . " files in bike_images directory\n";
    } else {
        echo "✗ bike_images directory doesn't exist\n";
    }
} else {
    echo "✗ Storage app/public directory doesn't exist\n";
}

// Test if a specific file can be accessed via public URL
echo "\nTesting public access to bike images\n";
echo "=================================\n";

// If any image files were found, test access to the first one
if (!empty($imageFiles)) {
    $testFile = $imageFiles[0];

    $publicUrl = '/storage/bike_images/' . $testFile;
    echo "Test image public URL would be: {$publicUrl}\n";

    // Check if the file can be accessed via the public path
    $publicFilePath = __DIR__ . '/public' . $publicUrl;
    if (file_exists($publicFilePath)) {
        echo "✓ Test file exists at public path: {$publicFilePath}\n";
    } else {
        echo "✗ Test file does NOT exist at public path: {$publicFilePath}\n";
    }
} else {
    echo "No image files found to test.\n";
}
