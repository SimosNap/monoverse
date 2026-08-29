<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\NumberHelper;

class MediaService
{
	private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50 MB
	
	private const ALLOWED_MIME_TYPES = [
	
		// Images
		'image/jpeg',
		'image/png',
		'image/webp',
		'image/gif',
		'image/avif',
	
		// Documents
		'application/pdf',
	
		// Audio
		'audio/mpeg',
		'audio/mp3',
		'audio/ogg',
		'audio/flac',
		'audio/wav',
		'audio/x-wav',
		'audio/mp4',
		'audio/aac',
	
		// Video
		'video/mp4',
		'video/webm',
		'video/ogg',
		'video/quicktime',
	
	];
	
	private const EXTENSIONS = [
	
		'image/jpeg'        => 'jpg',
		'image/png'         => 'png',
		'image/webp'        => 'webp',
		'image/gif'         => 'gif',
		'image/avif'        => 'avif',
	
		'application/pdf'   => 'pdf',
	
		'audio/mpeg'        => 'mp3',
		'audio/mp3'         => 'mp3',
		'audio/ogg'         => 'ogg',
		'audio/flac'        => 'flac',
		'audio/wav'         => 'wav',
		'audio/x-wav'       => 'wav',
		'audio/mp4'         => 'm4a',
		'audio/aac'         => 'aac',
	
		'video/mp4'         => 'mp4',
		'video/webm'        => 'webm',
		'video/ogg'         => 'ogv',
		'video/quicktime'   => 'mov',
	
	];
	
	private string $storagePath;

	public function __construct(
		private Database $database,
		private SettingsService $settings
	) {
		$this->storagePath = dirname(__DIR__, 2) . '/storage/media';
	}
	
	public function validate(array $files): void
	{
		if (empty($files)) {
			return;
		}
	
		$files = $this->normalizeFiles($files);
	
		$this->validateUploadSet($files);
	}

	public function store(
		int $postId,
		string $authorSub,
		array $files,
		?string $audioTitle = null,
		?string $audioArtist = null,
		?string $audioTracklist = null
	): void
	{
		if (empty($files)) {
			return;
		}
		
		$audioTitle = trim((string) $audioTitle);
		$audioArtist = trim((string) $audioArtist);
		$audioTracklist = trim((string) $audioTracklist);
		
		$audioTitle = $audioTitle !== ''
			? mb_substr($audioTitle, 0, 255)
			: null;
		
		$audioArtist = $audioArtist !== ''
			? mb_substr($audioArtist, 0, 255)
			: null;
		
		$audioTracklist = $audioTracklist !== ''
			? $audioTracklist
			: null;
		
		$files = $this->normalizeFiles($files);
		
		foreach ($files as $file) {
	
			if (
				!is_array($file) ||
				(($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
			) {
				continue;
			}
	
			$mimeType = $this->validateUpload($file);
	
			$mediaType = $this->detectMediaType($mimeType);
	
			$storageName = $this->buildStorageFilename($mimeType);
	
			$saved = $this->saveFile(
				$file,
				$mediaType,
				$mimeType,
				$storageName
			);
	
			$this->insertMedia(
				$postId,
				pathinfo($storageName, PATHINFO_FILENAME),
				$mediaType,
				$saved['storage_path'],
				$file['name'],
				$saved['mime_type'],
				(int) $file['size'],
				$saved['width'],
				$saved['height'],
				$saved['hash'],
				$mediaType === 'audio'
					? $audioTitle
					: null,
				$mediaType === 'audio'
					? $audioArtist
					: null,
				$mediaType === 'audio'
					? $audioTracklist
					: null
			);
		}
	}
	
	private function normalizeFiles(array $files): array
	{
		if (!isset($files['name']) || !is_array($files['name'])) {
			return [];
		}
	
		$normalized = [];
	
		foreach (array_keys($files['name']) as $index) {
	
			$normalized[] = [
				'name'     => $files['name'][$index],
				'type'     => $files['type'][$index],
				'tmp_name' => $files['tmp_name'][$index],
				'error'    => $files['error'][$index],
				'size'     => $files['size'][$index],
			];
		}
	
		return $normalized;
	}
	
	private function getMediaDirectory(string $mediaType): string
	{
		return match ($mediaType) {
			'image'   => 'images',
			'video'   => 'videos',
			'audio'   => 'audio',
			'document'=> 'documents',
			default   => throw new \InvalidArgumentException(
				sprintf('Unsupported media type "%s".', $mediaType)
			),
		};
	}
	
	private function ensureStorageDirectory(string $mediaType): string
	{
		$directory = sprintf(
			'%s/%s/%s/%s',
			$this->storagePath,
			$this->getMediaDirectory($mediaType),
			date('Y'),
			date('m')
		);
	
		if (!is_dir($directory)) {
			mkdir($directory, 0775, true);
		}
	
		return $directory;
	}
	
	private function validateUpload(array $file): string
	{
		if (!isset(
			$file['error'],
			$file['tmp_name'],
			$file['size']
		)) {
			throw new \RuntimeException('Invalid upload.');
		}
	
		if ((int) $file['error'] !== UPLOAD_ERR_OK) {
			throw new \RuntimeException(
				sprintf(
					'Upload failed with error code %d.',
					(int) $file['error']
				)
			);
		}
	
		if (!is_uploaded_file((string) $file['tmp_name'])) {
			throw new \RuntimeException(
				'Invalid uploaded file.'
			);
		}
	
		if ((int) $file['size'] <= 0) {
			throw new \RuntimeException(
				'The uploaded file is empty.'
			);
		}
	
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
	
		$mimeType = $finfo->file((string) $file['tmp_name']);
	
		if (!is_string($mimeType) || $mimeType === '') {
			throw new \RuntimeException(
				'Unable to determine file type.'
			);
		}
	
		$mimeType = strtolower(trim($mimeType));
	
		if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
			throw new \RuntimeException(
				sprintf(
					'Unsupported MIME type "%s".',
					$mimeType
				)
			);
		}
	
		$mediaType = $this->detectMediaType($mimeType);
	
		$maxFileSize = self::MAX_FILE_SIZE;
	
		if ($mediaType === 'audio') {
	
			if (
				$this->settings->get(
					'media_audio_upload_enabled',
					'1'
				) !== '1'
			) {
				throw new \RuntimeException(
					'Il caricamento di allegati audio non è consentito.'
				);
			}
	
			$maxMb = max(
				1,
				(int) $this->settings->get(
					'media_audio_max_mb',
					'50'
				)
			);
	
			$maxFileSize = $maxMb * 1024 * 1024;
		}
	
		if ($mediaType === 'video') {
	
			if (
				$this->settings->get(
					'media_video_upload_enabled',
					'1'
				) !== '1'
			) {
				throw new \RuntimeException(
					'Il caricamento di allegati video non è consentito.'
				);
			}
	
			$maxMb = max(
				1,
				(int) $this->settings->get(
					'media_video_max_mb',
					'50'
				)
			);
	
			$maxFileSize = $maxMb * 1024 * 1024;
		}
	
		if ((int) $file['size'] > $maxFileSize) {
			throw new \RuntimeException(
				sprintf(
					'Il file supera la dimensione massima consentita (%d MB).',
					(int) ($maxFileSize / 1024 / 1024)
				)
			);
		}
	
		return $mimeType;
	}
	
	private function detectMediaType(string $mimeType): string
	{
		return match (true) {
	
			str_starts_with($mimeType, 'image/')
				=> 'image',
	
			str_starts_with($mimeType, 'video/')
				=> 'video',
	
			str_starts_with($mimeType, 'audio/')
				=> 'audio',
	
			$mimeType === 'application/pdf'
				=> 'document',
	
			default
				=> throw new \RuntimeException(
					sprintf(
						'Unsupported media type "%s".',
						$mimeType
					)
				),
		};
	}
	
	private function buildStorageFilename(string $mimeType): string
	{
		if (!isset(self::EXTENSIONS[$mimeType])) {
			throw new \RuntimeException(
				sprintf(
					'Unknown extension for MIME "%s".',
					$mimeType
				)
			);
		}
	
		return sprintf(
			'%s.%s',
			\Monoverse\Helpers\Uuid::v4(),
			self::EXTENSIONS[$mimeType]
		);
	}
	
	private function saveFile(
		array $file,
		string $mediaType,
		string $mimeType,
		string $storageName
	): array
	{
		$directory = $this->ensureStorageDirectory($mediaType);
	
		$destination = $directory . '/' . $storageName;
		$previewPath = null;
	
		if (!move_uploaded_file($file['tmp_name'], $destination)) {
			throw new \RuntimeException(
				'Unable to save uploaded file.'
			);
		}
	
		try {
			$hash = hash_file('sha256', $destination);
	
			if ($hash === false) {
				throw new \RuntimeException(
					'Unable to calculate file hash.'
				);
			}
	
			$width = null;
			$height = null;
	
			if ($mediaType === 'image') {
				$size = @getimagesize($destination);
	
				if ($size === false) {
					throw new \RuntimeException(
						'Unable to read image dimensions.'
					);
				}
	
				$width = (int) $size[0];
				$height = (int) $size[1];
	
				$previewPath = preg_replace(
					'/\.[^.]+$/',
					'.webp',
					$destination
				);
	
				if (!is_string($previewPath)) {
					throw new \RuntimeException(
						'Unable to build preview path.'
					);
				}
	
				$this->createImagePreview(
					$destination,
					$previewPath,
					$mimeType
				);
			}
			
			if ($mediaType === 'audio') {
			
				$previewPath = preg_replace(
					'/\.[^.]+$/',
					'.waveform.png',
					$destination
				);
			
				if (!is_string($previewPath)) {
					throw new \RuntimeException(
						'Unable to build audio waveform path.'
					);
				}
			
				$this->createAudioWaveform(
					$destination,
					$previewPath
				);
			}
			
			if ($mediaType === 'video') {
			
				$previewPath = preg_replace(
					'/\.[^.]+$/',
					'.preview.jpg',
					$destination
				);
			
				if (!is_string($previewPath)) {
					throw new \RuntimeException(
						'Unable to build video preview path.'
					);
				}
			
				$this->createVideoPreview(
					$destination,
					$previewPath
				);
			}
	
			$relativePath = sprintf(
				'%s/%s/%s/%s',
				$this->getMediaDirectory($mediaType),
				date('Y'),
				date('m'),
				$storageName
			);
	
			return [
				'storage_path' => $relativePath,
				'mime_type'    => $mimeType,
				'hash'         => $hash,
				'width'        => $width,
				'height'       => $height,
			];
		} catch (\Throwable $exception) {
			@unlink($destination);
	
			if (
				is_string($previewPath) &&
				is_file($previewPath)
			) {
				@unlink($previewPath);
			}
	
			throw $exception;
		}
	}
	
	private function insertMedia(
		int $postId,
		string $uuid,
		string $mediaType,
		string $storagePath,
		string $originalName,
		string $mimeType,
		int $fileSize,
		?int $width,
		?int $height,
		?string $hash,
		?string $audioTitle,
		?string $audioArtist,
		?string $audioTracklist
	): void
	{
		$this->database->execute(
			'
			INSERT INTO community_post_media
			(
				post_id,
				uuid,
				media_type,
				storage_path,
				original_name,
				mime_type,
				file_size,
				hash,
				width,
				height,
				audio_title,
				audio_artist,
				audio_tracklist,
				status,
				created_at,
				updated_at
			)
			VALUES
			(
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
			',
			[
				$postId,
				$uuid,
				$mediaType,
				$storagePath,
				$originalName,
				$mimeType,
				$fileSize,
				$hash,
				$width,
				$height,
				$audioTitle,
				$audioArtist,
				$audioTracklist,
				'active',
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
			]
		);
	}
	
	public function deleteByPostId(int $postId): void
	{
		$media = $this->findByPostId($postId);
	
		foreach ($media as $item) {
	
			if (!empty($item['storage_path'])) {
	
				$file = $this->storagePath . '/' . ltrim($item['storage_path'], '/');
	
				if (is_file($file)) {
					@unlink($file);
				}
	
				if (($item['media_type'] ?? '') === 'image') {
	
					$preview = preg_replace(
						'/\.[^.]+$/',
						'.webp',
						$file
					);
	
					if (is_string($preview) && is_file($preview)) {
						@unlink($preview);
					}
				}
				
				if (($item['media_type'] ?? '') === 'audio') {
				
					$waveform = preg_replace(
						'/\.[^.]+$/',
						'.waveform.png',
						$file
					);
				
					if (
						is_string($waveform) &&
						is_file($waveform)
					) {
						@unlink($waveform);
					}
				}
				
				if (($item['media_type'] ?? '') === 'video') {
				
					$preview = preg_replace(
						'/\.[^.]+$/',
						'.preview.jpg',
						$file
					);
				
					if (
						is_string($preview) &&
						is_file($preview)
					) {
						@unlink($preview);
					}
				}
			}
		}
	}
	
	public function findByPostId(int $postId): array
	{
		$media = $this->database->fetchAll(
			'
			SELECT *
			FROM community_post_media
			WHERE post_id = ?
			  AND status = ?
			  AND deleted_at IS NULL
			ORDER BY sort_order ASC, id ASC
			',
			[
				$postId,
				'active',
			]
		);
		
		foreach ($media as &$item) {
		
			$item['public_url'] = $this->getPublicUrl($item);
			
			$item['waveform_url'] = '';
			
			if (
				($item['media_type'] ?? '') === 'audio'
				&& !empty($item['storage_path'])
			) {
				$waveformPath = preg_replace(
					'/\.[^.]+$/',
					'.waveform.png',
					(string) $item['storage_path']
				);
			
				if (is_string($waveformPath)) {
			
					$waveformAbsolute =
						$this->storagePath
						. '/'
						. ltrim(
							$waveformPath,
							'/'
						);
			
					if (is_file($waveformAbsolute)) {
						$item['waveform_url'] =
							'/storage/media/'
							. ltrim(
								$waveformPath,
								'/'
							);
					}
				}
			}
			
			$item['preview_url'] = '';
			
			if (
				($item['media_type'] ?? '') === 'video'
				&& !empty($item['storage_path'])
			) {
				$previewPath = preg_replace(
					'/\.[^.]+$/',
					'.preview.jpg',
					(string) $item['storage_path']
				);
			
				if (is_string($previewPath)) {
			
					$previewAbsolute =
						$this->storagePath
						. '/'
						. ltrim(
							$previewPath,
							'/'
						);
			
					if (is_file($previewAbsolute)) {
						$item['preview_url'] =
							'/storage/media/'
							. ltrim(
								$previewPath,
								'/'
							);
					}
				}
			}
		
			$item['formatted_size'] = NumberHelper::bytes(
				(int) ($item['file_size'] ?? 0)
			);
		}
		
		unset($item);
		
		return $media;
	}
	
	public function getPublicUrl(array $media): string
	{
		if (empty($media['storage_path'])) {
			return '';
		}
	
		$path = (string) $media['storage_path'];
	
		if (($media['media_type'] ?? '') === 'image') {
	
			$webp = preg_replace(
				'/\.[^.]+$/',
				'.webp',
				$path
			);
	
			$absolute = $this->storagePath . '/' . $webp;
	
			if (is_file($absolute)) {
				return '/storage/media/' . ltrim($webp, '/');
			}
		}
	
		return '/storage/media/' . ltrim($path, '/');
	}
	
	private function validateUploadSet(array $files): void
	{
		$mediaType = null;
		$count = 0;
	
		foreach ($files as $file) {
	
			if (
				!is_array($file) ||
				(($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
			) {
				continue;
			}
	
			$type = $this->detectMediaType(
				$this->validateUpload($file)
			);
	
			if ($mediaType === null) {
				$mediaType = $type;
			}
	
			if ($mediaType !== $type) {
				throw new \RuntimeException(
					'Puoi allegare solo un tipo di file per ogni Ping.'
				);
			}
	
			$count++;
		}
	
		if ($count === 0) {
			return;
		}
	
		switch ($mediaType) {
	
			case 'image':
				$limit = 10;
				break;
	
			case 'document':
				$limit = 5;
				break;
	
			case 'video':
			case 'audio':
				$limit = 1;
				break;
	
			default:
				$limit = 0;
				break;
		}
	
		if ($count > $limit) {
			throw new \RuntimeException(
				sprintf(
					'È possibile allegare al massimo %d file di tipo %s.',
					$limit,
					$mediaType
				)
			);
		}
	}
	
	private function createVideoPreview(
		string $sourcePath,
		string $destinationPath
	): void
	{
		$ffmpeg = '/usr/bin/ffmpeg';
	
		if (!is_file($ffmpeg)) {
			throw new \RuntimeException(
				'FFmpeg is not available.'
			);
		}
	
		// Frame a 1 secondo.
		$command = sprintf(
			'%s -hide_banner -loglevel error -y '
			. '-ss 1 '
			. '-i %s '
			. '-frames:v 1 '
			. '-vf %s '
			. '%s 2>&1',
			escapeshellarg($ffmpeg),
			escapeshellarg($sourcePath),
			escapeshellarg(
				'scale=640:-2'
			),
			escapeshellarg($destinationPath)
		);
	
		$output = [];
		$exitCode = 0;
	
		exec(
			$command,
			$output,
			$exitCode
		);
	
		if (
			$exitCode === 0
			&& is_file($destinationPath)
			&& filesize($destinationPath) > 0
		) {
			return;
		}
	
		// Fallback per video molto corti.
		@unlink($destinationPath);
	
		$command = sprintf(
			'%s -hide_banner -loglevel error -y '
			. '-i %s '
			. '-frames:v 1 '
			. '-vf %s '
			. '%s 2>&1',
			escapeshellarg($ffmpeg),
			escapeshellarg($sourcePath),
			escapeshellarg(
				'scale=640:-2'
			),
			escapeshellarg($destinationPath)
		);
	
		$output = [];
		$exitCode = 0;
	
		exec(
			$command,
			$output,
			$exitCode
		);
	
		if (
			$exitCode !== 0
			|| !is_file($destinationPath)
			|| filesize($destinationPath) === 0
		) {
			throw new \RuntimeException(
				'Unable to generate video preview.'
			);
		}
	}
	
	private function createAudioWaveform(
		string $sourcePath,
		string $destinationPath
	): void
	{
		$ffmpeg = '/usr/bin/ffmpeg';
	
		if (!is_file($ffmpeg)) {
			throw new \RuntimeException(
				'FFmpeg is not available.'
			);
		}
	
		$command = sprintf(
			'%s -hide_banner -loglevel error -y '
			. '-i %s '
			. '-filter_complex %s '
			. '-frames:v 1 %s 2>&1',
			escapeshellarg($ffmpeg),
			escapeshellarg($sourcePath),
			escapeshellarg(
				'aformat=channel_layouts=mono,'
				. 'showwavespic=s=1000x120:colors=white'
			),
			escapeshellarg($destinationPath)
		);
	
		$output = [];
		$exitCode = 0;
	
		exec(
			$command,
			$output,
			$exitCode
		);
	
		if (
			$exitCode !== 0
			|| !is_file($destinationPath)
			|| filesize($destinationPath) === 0
		) {
			throw new \RuntimeException(
				'Unable to generate audio waveform.'
			);
		}
	}
	
	private function createImagePreview(
		string $sourcePath,
		string $destinationPath,
		string $mimeType,
		int $maxWidth = 1280,
		int $quality = 82
	): void
	{
		$image = match ($mimeType) {
			'image/jpeg' => @imagecreatefromjpeg($sourcePath),
			'image/png'  => @imagecreatefrompng($sourcePath),
			'image/webp' => @imagecreatefromwebp($sourcePath),
			default      => false,
		};
	
		if ($image === false) {
			throw new \RuntimeException(
				'Unable to create image preview.'
			);
		}
	
		$sourceWidth = imagesx($image);
		$sourceHeight = imagesy($image);
	
		if ($sourceWidth <= 0 || $sourceHeight <= 0) {
			imagedestroy($image);
	
			throw new \RuntimeException(
				'Invalid image dimensions.'
			);
		}
	
		$targetWidth = min($sourceWidth, $maxWidth);
	
		$targetHeight = (int) round(
			$sourceHeight * ($targetWidth / $sourceWidth)
		);
	
		$preview = imagecreatetruecolor(
			$targetWidth,
			$targetHeight
		);
	
		if ($preview === false) {
			imagedestroy($image);
	
			throw new \RuntimeException(
				'Unable to create preview canvas.'
			);
		}
	
		imagealphablending($preview, false);
		imagesavealpha($preview, true);
	
		imagecopyresampled(
			$preview,
			$image,
			0,
			0,
			0,
			0,
			$targetWidth,
			$targetHeight,
			$sourceWidth,
			$sourceHeight
		);
	
		if (!imagewebp($preview, $destinationPath, $quality)) {
			imagedestroy($preview);
			imagedestroy($image);
	
			throw new \RuntimeException(
				'Unable to save WebP preview.'
			);
		}
	
		imagedestroy($preview);
		imagedestroy($image);
	}
}