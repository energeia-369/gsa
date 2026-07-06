<?php
/**
 * Media Utility functions for handling image uploads,
 * automatic WebP conversion, and basic compression.
 */

class MediaUtils {
    
    public static function processAndUploadImage($fileArray, $uploadDir) {
        if (!isset($fileArray) || $fileArray['error'] !== 0) {
            return false;
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tmpName = $fileArray['tmp_name'];
        $originalName = basename($fileArray['name']);
        
        // Ensure unique filename
        $fileNameNoExt = time() . '_' . uniqid();
        $targetWebpPath = $uploadDir . $fileNameNoExt . '.webp';

        // Get image type
        $imageType = function_exists('exif_imagetype') ? exif_imagetype($tmpName) : false;
        $image = null;
        
        // If GD extension is not installed, fallback to moving the original file
        if (!function_exists('imagecreatefrompng') || !$imageType) {
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            if(empty($ext)) $ext = 'jpg';
            $targetFallback = $uploadDir . $fileNameNoExt . '.' . $ext;
            if (move_uploaded_file($tmpName, $targetFallback)) {
                return $targetFallback;
            }
            return false;
        }
        $image = null;

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($tmpName);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($tmpName);
                // Handle transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case IMAGETYPE_WEBP:
                // Already WebP, just move it
                if (move_uploaded_file($tmpName, $targetWebpPath)) {
                    return $targetWebpPath;
                }
                return false;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($tmpName);
                break;
            default:
                // Unsupported type, just use regular move_uploaded_file fallback
                $fallbackPath = $uploadDir . time() . '_' . $originalName;
                if (move_uploaded_file($tmpName, $fallbackPath)) {
                    return $fallbackPath;
                }
                return false;
        }

        if ($image !== false) {
            // Convert and save as WebP with 80% quality compression
            if (imagewebp($image, $targetWebpPath, 80)) {
                imagedestroy($image);
                return $targetWebpPath;
            }
            imagedestroy($image);
        }

        return false;
    }
}
?>
