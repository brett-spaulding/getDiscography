<?php

namespace App\Utils;


class ImageUrl
{

    /**
     * Modify the width and height values in a Google Image URL.
     *
     * @param string $url The original URL.
     * @return string The modified URL with new width and height values.
     */
    public static function modifyGoogleImageUrl(string $url, int $size = 1024): string
    {
        // Regular expression pattern to match width and height values in the URL
        $pattern = '/w(\\d+)-h(\\d+)/';

        // Use preg_replace_callback() to replace the matched values with the provided integer
        return preg_replace_callback($pattern, function ($matches) use ($size) {
            return "w{$size}-h{$size}";
        }, $url);
    }

    /**
     * Save an image from a Google Image URL to the local filesystem.
     *
     * @param string $url The modified URL of the image to download.
     * @param string $type The public directory to save to, needs to be either 'artist' or 'album'.
     * @return string The path to the saved image file, or empty string if the file already exists.
     */
    public static function save_img_url(string $url, string $type): string
    {

        // Get the filename from the URL
        $filename = basename($url);

        // Create a directory for the images (if it doesn't exist)
        $imagesDir = public_path('images/' . $type);
        if (!is_dir($imagesDir)) {
            mkdir($imagesDir, 0777, true);
        }

        // Check if the file already exists
        $imagePath = $imagesDir . '/' . $filename . '.jpg';
        if (file_exists($imagePath)) {
            return $imagePath; // File already exists, don't save again
        }

        // Download the image from the URL using curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode !== 200) {
            // Handle HTTP error (e.g. file not found)
            return '';
        }

        // Save the image to disk
        file_put_contents($imagePath, $response);

        return $imagePath;
    }

}
