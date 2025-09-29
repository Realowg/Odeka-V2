<?php

namespace App\Http\Controllers;

use InvalidArgumentException;
use Illuminate\Support\Facades\Log;

class SecureFileValidator
{
    /**
     * Allowed MIME types with their corresponding extensions
     */
    private static $allowedTypes = [
        // Images
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],
        
        // Videos
        'video/mp4' => ['mp4'],
        'video/quicktime' => ['mov'],
        'video/x-msvideo' => ['avi'],
        'video/webm' => ['webm'],
        
        // Audio
        'audio/mpeg' => ['mp3'],
        'audio/mp4' => ['m4a'],
        'audio/wav' => ['wav'],
        'audio/ogg' => ['ogg'],
        
        // Documents (if needed)
        'application/pdf' => ['pdf'],
        'text/plain' => ['txt']
    ];

    /**
     * Maximum file sizes by type (in bytes)
     */
    private static $maxSizes = [
        'image' => 10 * 1024 * 1024, // 10MB
        'video' => 500 * 1024 * 1024, // 500MB
        'audio' => 50 * 1024 * 1024, // 50MB
        'document' => 10 * 1024 * 1024 // 10MB
    ];

    /**
     * Validate uploaded file for security issues
     *
     * @param array $file The uploaded file array from $_FILES
     * @param string $fileType Expected file type category
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function validateFile(array $file, string $fileType = 'any'): bool
    {
        // Basic file validation
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new InvalidArgumentException('Invalid file upload');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('File upload error: ' . self::getUploadErrorMessage($file['error']));
        }

        // Check file size
        if ($file['size'] <= 0) {
            throw new InvalidArgumentException('Empty file not allowed');
        }

        // Get actual MIME type using file info
        $actualMimeType = self::getActualMimeType($file['tmp_name']);
        
        if (!$actualMimeType) {
            throw new InvalidArgumentException('Cannot determine file type');
        }

        // Validate MIME type
        if (!array_key_exists($actualMimeType, self::$allowedTypes)) {
            throw new InvalidArgumentException('File type not allowed: ' . $actualMimeType);
        }

        // Validate file extension matches MIME type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, self::$allowedTypes[$actualMimeType])) {
            throw new InvalidArgumentException('File extension does not match file type');
        }

        // Check file size based on type
        $category = self::getFileCategory($actualMimeType);
        if (isset(self::$maxSizes[$category]) && $file['size'] > self::$maxSizes[$category]) {
            throw new InvalidArgumentException('File too large for type: ' . $category);
        }

        // Additional security checks based on file type
        self::performTypeSpecificValidation($file['tmp_name'], $actualMimeType);

        // Check for malicious content
        self::scanForMaliciousContent($file['tmp_name'], $file['name']);

        return true;
    }

    /**
     * Get actual MIME type using file info
     */
    private static function getActualMimeType(string $filePath): ?string
    {
        if (!function_exists('finfo_open')) {
            Log::warning('finfo extension not available for file type detection');
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return $mimeType ?: null;
    }

    /**
     * Get file category from MIME type
     */
    private static function getFileCategory(string $mimeType): string
    {
        if (strpos($mimeType, 'image/') === 0) return 'image';
        if (strpos($mimeType, 'video/') === 0) return 'video';
        if (strpos($mimeType, 'audio/') === 0) return 'audio';
        return 'document';
    }

    /**
     * Perform type-specific validation
     */
    private static function performTypeSpecificValidation(string $filePath, string $mimeType): void
    {
        $category = self::getFileCategory($mimeType);

        switch ($category) {
            case 'image':
                self::validateImage($filePath, $mimeType);
                break;
            case 'video':
                self::validateVideo($filePath);
                break;
            case 'audio':
                self::validateAudio($filePath);
                break;
        }
    }

    /**
     * Validate image files
     */
    private static function validateImage(string $filePath, string $mimeType): void
    {
        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            throw new InvalidArgumentException('Invalid image file');
        }

        // Check dimensions
        [$width, $height] = $imageInfo;
        if ($width > 8000 || $height > 8000) {
            throw new InvalidArgumentException('Image dimensions too large');
        }

        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Invalid image dimensions');
        }

        // Verify MIME type matches getimagesize result
        $expectedMime = $imageInfo['mime'] ?? '';
        if ($expectedMime !== $mimeType) {
            throw new InvalidArgumentException('Image MIME type mismatch');
        }
    }

    /**
     * Validate video files
     */
    private static function validateVideo(string $filePath): void
    {
        // Basic validation - can be enhanced with FFmpeg if available
        $fileSize = filesize($filePath);
        if ($fileSize > self::$maxSizes['video']) {
            throw new InvalidArgumentException('Video file too large');
        }
    }

    /**
     * Validate audio files
     */
    private static function validateAudio(string $filePath): void
    {
        // Basic validation - can be enhanced with audio analysis if needed
        $fileSize = filesize($filePath);
        if ($fileSize > self::$maxSizes['audio']) {
            throw new InvalidArgumentException('Audio file too large');
        }
    }

    /**
     * Scan for malicious content patterns
     */
    private static function scanForMaliciousContent(string $filePath, string $fileName): void
    {
        // Check filename for dangerous patterns
        if (preg_match('/\.(php|phtml|php3|php4|php5|phar|jsp|asp|aspx|cgi|pl|py|rb|sh|bat|cmd|exe|scr|vbs|js|jar)$/i', $fileName)) {
            throw new InvalidArgumentException('Dangerous file extension detected');
        }

        // Check for PHP tags in file content (basic scan)
        $content = file_get_contents($filePath, false, null, 0, 1024); // Read first 1KB
        if (preg_match('/<\?php|<\?=|<script|<iframe|javascript:/i', $content)) {
            throw new InvalidArgumentException('Potentially malicious content detected');
        }

        // Check for double extensions
        if (preg_match('/\.[^.]+\.[^.]+$/i', $fileName)) {
            $parts = explode('.', $fileName);
            if (count($parts) > 2) {
                $secondExt = strtolower($parts[count($parts) - 2]);
                if (in_array($secondExt, ['php', 'asp', 'jsp', 'cgi', 'pl', 'py', 'sh'])) {
                    throw new InvalidArgumentException('Double extension detected');
                }
            }
        }
    }

    /**
     * Get human-readable upload error message
     */
    private static function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    /**
     * Generate secure filename
     */
    public static function generateSecureFilename(string $originalName, string $userId = null): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $userId = $userId ?: (auth()->id() ?: 'anonymous');
        
        return $userId . '_' . uniqid() . '_' . time() . '.' . $extension;
    }
}