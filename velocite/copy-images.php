<?php

echo "Copying bike images to public directory\n";
echo "====================================\n\n";

// Define directory paths
$sourceDir = __DIR__ . '/storage/app/public/bike_images';
$targetDir = __DIR__ . '/public/storage/bike_images';

// Check if source directory exists
if (!is_dir($sourceDir)) {
    die("Error: Source directory does not exist: $sourceDir\n");
}

// Create target directory if it doesn't exist
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        die("Error: Failed to create target directory: $targetDir\n");
    }
    echo "Created target directory: $targetDir\n";
}

// Copy files from source to target
$files = scandir($sourceDir);
$count = 0;
$errors = 0;

foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $sourcePath = $sourceDir . '/' . $file;
        $targetPath = $targetDir . '/' . $file;

        if (is_file($sourcePath)) {
            echo "Copying: $file ... ";
            if (copy($sourcePath, $targetPath)) {
                echo "SUCCESS\n";
                $count++;
            } else {
                echo "FAILED\n";
                $errors++;
            }
        }
    }
}

echo "\nSummary:\n";
echo "- $count files copied successfully\n";
echo "- $errors files failed to copy\n";

if ($count > 0) {
    echo "\nImage files should now be accessible via public URLs like:\n";
    echo "/storage/bike_images/[filename]\n";
}
