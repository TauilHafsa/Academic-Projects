<?php
/*
 * JavaScript build checker
 *
 * This script checks if the compiled JavaScript files exist and displays their last modified time
 */

// Security - only run in local environment
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

// Helper function to format timestamp
function formatTime($timestamp) {
    return date('Y-m-d H:i:s', $timestamp);
}

// List of directories to check
$directories = [
    'public/build/assets/',
];

echo "<h1>JavaScript Build Checker</h1>";

foreach ($directories as $dir) {
    echo "<h2>Directory: $dir</h2>";

    if (!is_dir($dir)) {
        echo "<p>Directory does not exist!</p>";
        continue;
    }

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>File</th><th>Size</th><th>Last Modified</th></tr>";

    $files = glob($dir . "*.js");
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    if (empty($files)) {
        echo "<tr><td colspan='3'>No JavaScript files found in this directory</td></tr>";
    } else {
        foreach ($files as $file) {
            $filename = basename($file);
            $size = filesize($file);
            $modified = filemtime($file);

            echo "<tr>";
            echo "<td>$filename</td>";
            echo "<td>" . number_format($size / 1024, 2) . " KB</td>";
            echo "<td>" . formatTime($modified) . "</td>";
            echo "</tr>";
        }
    }

    echo "</table>";
}

echo "<h2>Source Files</h2>";

$sourceFiles = [
    'resources/js/app.js',
    'resources/js/notification.js',
    'resources/js/notification-realtime.js',
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>File</th><th>Size</th><th>Last Modified</th><th>Exists</th></tr>";

foreach ($sourceFiles as $file) {
    $exists = file_exists($file);

    echo "<tr>";
    echo "<td>$file</td>";

    if ($exists) {
        $size = filesize($file);
        $modified = filemtime($file);
        echo "<td>" . number_format($size / 1024, 2) . " KB</td>";
        echo "<td>" . formatTime($modified) . "</td>";
        echo "<td>Yes</td>";
    } else {
        echo "<td colspan='2'>N/A</td>";
        echo "<td>No</td>";
    }

    echo "</tr>";
}

echo "</table>";

echo "<h2>Vite Manifest Check</h2>";

$manifestFile = 'public/build/manifest.json';
if (file_exists($manifestFile)) {
    $manifest = json_decode(file_get_contents($manifestFile), true);

    echo "<p>Manifest file exists. Last modified: " . formatTime(filemtime($manifestFile)) . "</p>";

    if ($manifest) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Entry</th><th>Output File</th></tr>";

        foreach ($manifest as $entry => $data) {
            $outputFile = $data['file'] ?? 'N/A';
            echo "<tr><td>$entry</td><td>$outputFile</td></tr>";
        }

        echo "</table>";
    } else {
        echo "<p>Error parsing manifest file.</p>";
    }
} else {
    echo "<p>Manifest file does not exist.</p>";
}

// Check if we need to rebuild assets
echo "<h2>Build Status</h2>";

$needsRebuild = false;
$reasons = [];

if (!file_exists($manifestFile)) {
    $needsRebuild = true;
    $reasons[] = "Manifest file missing";
} else {
    foreach ($sourceFiles as $file) {
        if (file_exists($file)) {
            $sourceModified = filemtime($file);
            $manifestModified = filemtime($manifestFile);

            if ($sourceModified > $manifestModified) {
                $needsRebuild = true;
                $reasons[] = "Source file '$file' was modified after the build";
            }
        }
    }
}

if ($needsRebuild) {
    echo "<p>Status: <strong style='color:red'>Needs Rebuild</strong></p>";
    echo "<ul>";
    foreach ($reasons as $reason) {
        echo "<li>$reason</li>";
    }
    echo "</ul>";
    echo "<p>Run <code>npm run build</code> to rebuild the assets.</p>";
} else {
    echo "<p>Status: <strong style='color:green'>Up to date</strong></p>";
}
