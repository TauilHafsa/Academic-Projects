# Velocité Storage Setup

## Image Storage Solution

This document explains the image storage solution for the Vélocité project, particularly for bike images.

### Issue

The project was experiencing issues with displaying bike images. The problem was that the symbolic link between `storage/app/public` and `public/storage` was not working properly on Windows. As a result, bike images stored in `storage/app/public/bike_images` were not accessible via the web.

### Solution

We've implemented a file synchronization approach:

1. Bike images continue to be uploaded to `storage/app/public/bike_images` (the Laravel-standard location)
2. A synchronization script (`sync-images.php`) copies these files to `public/storage/bike_images` directory
3. The views are set up to look for images at `/storage/bike_images/[filename]`

### Usage

#### Manually Synchronize Images

If you add new bike images or if you notice that images are not displaying correctly, run:

```
php sync-images.php
```

This will:
- Copy new images from `storage/app/public/bike_images` to `public/storage/bike_images`
- Update any modified images
- Remove images from the public directory that no longer exist in the source
- Ensure proper access control via .htaccess

#### Automatic Synchronization

For production environments, it's recommended to set up a scheduled task:

1. **Windows Task Scheduler:**
   - Program: `php`
   - Arguments: `C:\path\to\velocite\sync-images.php`
   - Run weekly or after deployments

2. **Cron Job (Linux/Mac):**
   ```
   0 0 * * 0 php /path/to/velocite/sync-images.php
   ```

### Implementation Details

The synchronization script:
- Ensures the target directory exists
- Compares source and target files
- Copies only new or modified files
- Optionally removes deleted files
- Sets up proper access control
- Provides a detailed report of actions taken

### Troubleshooting

If images are not displaying:

1. Check if the images exist in `storage/app/public/bike_images`
2. Run `php sync-images.php` to ensure files are copied to public directory
3. Verify that file permissions allow web server access to `public/storage/bike_images`
4. Check browser developer tools for 404 errors on image paths
5. Ensure the paths in views use `asset('storage/bike_images/[filename]')`

### Alternative Solutions

If you prefer to use Laravel's standard approach:

1. Delete the existing `public/storage` directory
2. Run `php artisan storage:link` with administrator privileges
3. This may require additional configuration on Windows servers

### Notes for Developers

When adding new image upload functionality:
1. Store uploaded files in `storage/app/public/[directory]`
2. Add the new directory to the sync script if needed
3. Use `asset('storage/[directory]/[filename]')` in views 
