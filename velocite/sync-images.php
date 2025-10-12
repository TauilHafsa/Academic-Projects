<?php

/**
 * Image Synchronization Script for Vélocité
 *
 * This script synchronizes bike images from the private storage directory
 * to the public directory, ensuring they are accessible via the web.
 *
 * Usage:
 * - Run manually: php sync-images.php
 * - Add to cron job for automatic synchronization
 */

echo "Vélocité Image Synchronization\n";
echo "=============================\n\n";

// Define directory paths
$sourceDir = __DIR__ . '/storage/app/public/bike_images';
$targetDir = __DIR__ . '/public/storage/bike_images';

// Ensure target directory exists
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        die("Error: Failed to create target directory: $targetDir\n");
    }
    echo "Created target directory: $targetDir\n";
}

// Get files from both directories
$sourceFiles = is_dir($sourceDir) ? scandir($sourceDir) : [];
$targetFiles = is_dir($targetDir) ? scandir($targetDir) : [];

// Filter out . and ..
$sourceFiles = array_filter($sourceFiles, function($file) {
    return $file !== '.' && $file !== '..';
});

$targetFiles = array_filter($targetFiles, function($file) {
    return $file !== '.' && $file !== '..';
});

// Counters
$newFiles = 0;
$updatedFiles = 0;
$deletedFiles = 0;
$errors = 0;

// Copy new and updated files from source to target
foreach ($sourceFiles as $file) {
    $sourcePath = $sourceDir . '/' . $file;
    $targetPath = $targetDir . '/' . $file;

    // Skip directories
    if (!is_file($sourcePath)) {
        continue;
    }

    $needsCopy = false;

    // Check if file exists in target
    if (!file_exists($targetPath)) {
        echo "New file: $file\n";
        $needsCopy = true;
        $newFiles++;
    }
    // Check if source file is newer than target or has different size
    elseif (filemtime($sourcePath) > filemtime($targetPath) || filesize($sourcePath) !== filesize($targetPath)) {
        echo "Updated file: $file\n";
        $needsCopy = true;
        $updatedFiles++;
    }

    // Copy if needed
    if ($needsCopy) {
        if (!copy($sourcePath, $targetPath)) {
            echo "  ERROR: Failed to copy $file\n";
            $errors++;
        }
    }
}

// Remove files from target that don't exist in source (optional)
$removeOrphans = true; // Set to false if you want to keep removed files
if ($removeOrphans) {
    foreach ($targetFiles as $file) {
        $sourcePath = $sourceDir . '/' . $file;
        $targetPath = $targetDir . '/' . $file;

        if (!file_exists($sourcePath) && is_file($targetPath)) {
            echo "Removing obsolete file: $file\n";
            if (unlink($targetPath)) {
                $deletedFiles++;
            } else {
                echo "  ERROR: Failed to delete $file\n";
                $errors++;
            }
        }
    }
}

// Summary
echo "\nSynchronization Complete\n";
echo "----------------------\n";
echo "New files copied: $newFiles\n";
echo "Files updated: $updatedFiles\n";
echo "Obsolete files removed: $deletedFiles\n";
echo "Errors encountered: $errors\n";

// Create .htaccess file in target directory to allow access if needed
$htaccessFile = $targetDir . '/.htaccess';
if (!file_exists($htaccessFile)) {
    $htaccessContent = "# Allow access to image files\n" .
                      "Options -Indexes\n" .
                      "<IfModule mod_rewrite.c>\n" .
                      "    RewriteEngine On\n" .
                      "    RewriteCond %{REQUEST_FILENAME} -f\n" .
                      "    RewriteRule .* - [L]\n" .
                      "</IfModule>\n";

    if (file_put_contents($htaccessFile, $htaccessContent)) {
        echo "\nCreated .htaccess file for proper access control\n";
    } else {
        echo "\nWARNING: Failed to create .htaccess file\n";
    }
}
