<?php

echo "Storage Debug\n\n";

// Check main paths
$publicStoragePath = __DIR__ . '/public/storage';
$appPublicPath = __DIR__ . '/storage/app/public';
$bikeImagesSourcePath = $appPublicPath . '/bike_images';
$bikeImagesPublicPath = $publicStoragePath . '/bike_images';

echo "Checking paths:\n";
echo "- public/storage exists? " . (file_exists($publicStoragePath) ? "YES" : "NO") . "\n";
echo "- storage/app/public exists? " . (file_exists($appPublicPath) ? "YES" : "NO") . "\n";
echo "- storage/app/public/bike_images exists? " . (file_exists($bikeImagesSourcePath) ? "YES" : "NO") . "\n";
echo "- public/storage/bike_images exists? " . (file_exists($bikeImagesPublicPath) ? "YES" : "NO") . "\n";

// Check if public/storage is a symbolic link
echo "\nSymbolic link check:\n";
echo "- public/storage is symlink? " . (is_link($publicStoragePath) ? "YES" : "NO") . "\n";
if (is_link($publicStoragePath)) {
    echo "  Target: " . readlink($publicStoragePath) . "\n";
}

// List files in bike_images directory
if (is_dir($bikeImagesSourcePath)) {
    echo "\nFiles in storage/app/public/bike_images:\n";
    $files = scandir($bikeImagesSourcePath);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- $file\n";
        }
    }
} else {
    echo "\nCannot list files in bike_images directory (not found)\n";
}

// Try to recreate the symlink
echo "\nTrying to create/recreate the symbolic link...\n";
if (is_link($publicStoragePath)) {
    unlink($publicStoragePath);
    echo "Removed existing symbolic link\n";
}

// Create the directory structure
if (!is_dir($appPublicPath)) {
    mkdir($appPublicPath, 0755, true);
    echo "Created storage/app/public directory\n";
}

if (!is_dir($bikeImagesSourcePath)) {
    mkdir($bikeImagesSourcePath, 0755, true);
    echo "Created storage/app/public/bike_images directory\n";
}

// Create symlink for Windows (requires administrator privileges)
echo "Attempting to create symlink on Windows...\n";
try {
    exec('mklink /J "' . $publicStoragePath . '" "' . $appPublicPath . '"', $output, $returnVar);
    echo "Command result: " . ($returnVar === 0 ? "Success" : "Failed") . "\n";
    if (isset($output) && is_array($output)) {
        foreach ($output as $line) {
            echo "  " . $line . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error creating symlink: " . $e->getMessage() . "\n";
}

// Final check
echo "\nFinal check:\n";
echo "- public/storage exists? " . (file_exists($publicStoragePath) ? "YES" : "NO") . "\n";
echo "- public/storage is symlink? " . (is_link($publicStoragePath) ? "YES" : "NO") . "\n";
echo "- public/storage/bike_images exists? " . (file_exists($bikeImagesPublicPath) ? "YES" : "NO") . "\n";
