<?php

namespace App\Http\Controllers;

use Image;
use App\Helper;
use FileUploader;
use App\Models\Media;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UploadMediaController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * submit the form - only upload to temp storage, no database operations
	 */
	public function store(): JsonResponse
	{
		$publicPath = public_path('temp/');
		$file = strtolower(auth()->id() . uniqid() . time() . str_random(20));

		if (config('settings.video_encoding') == 'off') {
			$extensions = ['png', 'jpeg', 'jpg', 'gif', 'ief', 'video/mp4', 'audio/x-matroska', 'audio/mpeg'];
		} else {
			$extensions = [
				'png',
				'jpeg',
				'jpg',
				'gif',
				'ief',
				'video/mp4',
				'video/quicktime',
				'video/3gpp',
				'video/mpeg',
				'video/x-matroska',
				'video/x-ms-wmv',
				'video/vnd.avi',
				'video/avi',
				'video/x-flv',
				'audio/x-matroska',
				'audio/mpeg'
			];
		}

		// initialize FileUploader
		$FileUploader = new FileUploader('photo', array(
			'limit' => config('settings.maximum_files_post'),
			'fileMaxSize' => floor(config('settings.file_size_allowed') / 1024),
			'extensions' => $extensions,
			'title' => $file,
			'uploadDir' => $publicPath
		));

		// upload
		$upload = $FileUploader->upload();

		if ($upload['isSuccess']) {
			foreach ($upload['files'] as $key => $item) {
				// Process file based on type but keep in temp storage
				switch ($item['format']) {
					case 'image':
						$processedFile = $this->processImageTemp($item);
						break;

					case 'video':
						$processedFile = $this->processVideoTemp($item);
						break;

					case 'audio':
						$processedFile = $this->processAudioTemp($item);
						break;

					default:
						$processedFile = $item;
						break;
				}

				// Return file metadata for frontend to store
				$upload['files'][$key] = [
					'extension' => $item['extension'],
					'format' => $item['format'],
					'name' => $item['name'],
					'size' => $item['size'],
					'size2' => $item['size2'],
					'type' => $item['type'],
					'uploaded' => true,
					'replaced' => false,
					'tempPath' => 'temp/' . $item['name'], // Add temp path for later processing
					'metadata' => $processedFile['metadata'] ?? null // Store additional metadata
				];
			}
		}

		return response()->json($upload);
	}

	/**
	 * Process image in temp storage (resize and add watermark but keep in temp)
	 */
	protected function processImageTemp($image): array
	{
		$fileName = $image['name'];
		$pathImage = public_path('temp/') . $image['name'];
		$img = Image::make($pathImage);
		$url = ucfirst(Helper::urlToDomain(url('/')));

		$width = $img->width();
		$height = $img->height();

		if ($image['extension'] != 'gif') {
			// Image Large
			if ($width > 2000) {
				$scale = 2000;
			} else {
				$scale = $width;
			}

			// Calculate font size
			if ($width >= 400 && $width < 900) {
				$fontSize = 18;
			} elseif ($width >= 800 && $width < 1200) {
				$fontSize = 24;
			} elseif ($width >= 1200 && $width < 2000) {
				$fontSize = 32;
			} elseif ($width >= 2000 && $width < 3000) {
				$fontSize = 50;
			} elseif ($width >= 3000) {
				$fontSize = 75;
			} else {
				$fontSize = 0;
			}

			if (config('settings.watermark') == 'on') {
				$img->orientate()->resize($scale, null, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				})->text($url . '/' . auth()->user()->username, $img->width() - 30, $img->height() - 30, function ($font)
				use ($fontSize) {
					$font->file(public_path('webfonts/arial.TTF'));
					$font->size($fontSize);
					$font->color('#eaeaea');
					$font->align('right');
					$font->valign('bottom');
				})->save();
			} else {
				$img->orientate()->resize($scale, null, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				})->save();
			}
		}

		return [
			'metadata' => [
				'width' => $width,
				'height' => $height,
				'img_type' => $image['extension'] == 'gif' ? 'gif' : null,
				'bytes' => $image['size'],
				'mime' => $image['type']
			]
		];
	}

	/**
	 * Process video in temp storage (prepare metadata)
	 */
	protected function processVideoTemp($video): array
	{
		return [
			'metadata' => [
				'bytes' => $video['size'],
				'mime' => $video['type']
			]
		];
	}

	/**
	 * Process audio in temp storage (prepare metadata)
	 */
	protected function processAudioTemp($audio): array
	{
		return [
			'metadata' => [
				'bytes' => $audio['size'],
				'mime' => $audio['type']
			]
		];
	}

	/**
	 * Move file from temp to final storage and create database record
	 */
	public static function moveFromTempToStorage($tempFileName, $postId, $metadata = null): Media
	{
		$localFile = public_path('temp/' . $tempFileName);
		
		if (!file_exists($localFile)) {
			throw new \Exception("Temp file not found: " . $tempFileName);
		}

		$token = str_random(150) . uniqid() . now()->timestamp;
		$fileInfo = pathinfo($tempFileName);
		$extension = strtolower($fileInfo['extension']);
		
		// Determine file type and path
		if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
			$type = 'image';
			$path = config('path.images');
		} elseif (in_array($extension, ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv', '3gp', 'mpeg'])) {
			$type = 'video';
			$path = config('path.videos');
		} elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'flac', 'm4a'])) {
			$type = 'music';
			$path = config('path.music');
		} else {
			$type = 'file';
			$path = config('path.files');
		}

		// Move the file to final storage
		Storage::putFileAs($path, new File($localFile), $tempFileName);

		// Create database record
		$media = Media::create([
			'updates_id' => $postId,
			'user_id' => auth()->id(),
			'type' => $type,
			'image' => $type === 'image' ? $tempFileName : '',
			'video' => $type === 'video' ? $tempFileName : '',
			'music' => $type === 'music' ? $tempFileName : '',
			'file' => !in_array($type, ['image', 'video', 'music']) ? $tempFileName : '',
			'width' => $metadata['width'] ?? 0,
			'height' => $metadata['height'] ?? 0,
			'video_poster' => '',
			'video_embed' => '',
			'file_name' => '',
			'file_size' => '',
			'bytes' => $metadata['bytes'] ?? filesize($localFile),
			'mime' => $metadata['mime'] ?? mime_content_type($localFile),
			'img_type' => $metadata['img_type'] ?? '',
			'token' => $token,
			'status' => 'active',
			'created_at' => now()
		]);

		// Delete temp file
		unlink($localFile);

		return $media;
	}

	/**
	 * Delete a temp file (for when user removes file before submitting form)
	 */
	public function delete()
	{
		$fileName = $this->request->file;
		$tempFile = public_path('temp/' . $fileName);

		if (file_exists($tempFile)) {
			unlink($tempFile);
		}

		return response()->json([
			'success' => true
		]);
	}

	/**
	 * Clean up old temp files (can be called by a scheduled job)
	 */
	public static function cleanupOldTempFiles($hoursOld = 24)
	{
		$tempDir = public_path('temp/');
		$files = glob($tempDir . '*');
		$now = time();

		foreach ($files as $file) {
			if (is_file($file)) {
				if ($now - filemtime($file) >= $hoursOld * 3600) {
					unlink($file);
				}
			}
		}
	}
}
