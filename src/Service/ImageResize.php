<?php

namespace App\Services\ImageResize;

use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function file_exists;
use function imagecopy;
use function imagecreatefromgif;
use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagecreatefromwebp;
use function imagedestroy;
use function imagegif;
use function imagejpeg;
use function imagepng;
use function imagescale;
use function imagesx;
use function imagesy;
use function imagewebp;
use function implode;
use function in_array;
use function is_dir;
use function is_numeric;
use function is_readable;
use function is_writable;
use function round;
use function sprintf;
use function strtolower;

/**
 * Handles resizing images and watermarking.
 * Recommended not to use directly, instead use the ResizeService wrapper.
 * @author Kristo Leas <admin@kikopolis.tech>
 */
class ImageResize
{
	/**
	 * @var string
	 */
	private $uploadDirectory;
	
	/**
	 * @var string
	 */
	private $publicDirectory;
	
	/**
	 * @var string
	 */
	private $dbDirectory;
	
	/**
	 * @var string
	 */
	private $safePathName;
	
	/**
	 * @var UploadedFile
	 */
	private $image;
	
	/**
	 * @var bool|false|resource
	 */
	private $resource;
	
	/**
	 * @var string
	 */
	private $mime;
	
	/**
	 * @var string
	 */
	private $extension;
	
	/**
	 * @var array
	 */
	private $mimeTypes;
	
	/**
	 * @var array
	 */
	private $size;
	
	/**
	 * @var int[]
	 */
	private $outputSizes;
	
	/**
	 * @var array
	 */
	private $generated;
	
	/**
	 * @var string
	 */
	private $destination;
	
	/**
	 * @var bool
	 */
	private $useLongerDimension;
	
	/**
	 * @var int
	 */
	private $resample;
	
	/**
	 * @var int
	 */
	private $jpgCompression;
	
	/**
	 * @var int
	 */
	private $pngCompression;
	
	/**
	 * @var File
	 */
	private $watermark;
	
	/**
	 * @var string
	 */
	private $defaultWatermark;
	
	/**
	 * @var array
	 */
	private $allowedExtensions;
	
	/**
	 * @var bool
	 */
	private $isValid;
	
	/**
	 * @var string
	 */
	private $watermarkMime;
	
	/**
	 * @var false|resource
	 */
	private $watermarkResource;
	
	/**
	 * @var array
	 */
	private $watermarkSize;
	
	/**
	 * @var int
	 */
	private $watermarkBottomMargin;
	
	/**
	 * @var int
	 */
	private $watermarkRightMargin;
	
	/**
	 * @var bool
	 */
	private $watermarkImage;
	
	/**
	 * @var bool
	 */
	private $separateByMonth;
	
	/**
	 * @var bool
	 */
	private $separateByYear;
	
	/**
	 * @var int
	 */
	private $defaultThumbnailSize;
	
	/**
	 * @var string[]
	 */
	private $validOptions;
	
	/**
	 * @var array
	 */
	private $errors;
	
	/**
	 * @var int
	 */
	private $currentWorkingSize;
	
	/**
	 * @var bool|resource
	 */
	private $currentWorkingResource;
	
	/**
	 * @var string
	 */
	private $currentWorkingName;
	
	/**
	 * @var string
	 */
	private $currentWorkingPath;
	
	/**
	 * ImageResize constructor.
	 * @param  string  $uploadDirectory
	 * @param  string  $publicDirectory
	 * @param  string  $dbDirectory
	 * @throws Exception
	 */
	public function __construct(string $uploadDirectory, string $publicDirectory, string $dbDirectory)
	{
		$this->isValid               = false;
		$this->watermarkImage        = true;
		$this->errors                = [];
		$this->uploadDirectory       = rtrim($uploadDirectory, '/\\');
		$this->publicDirectory       = rtrim($publicDirectory, '/\\');
		$this->dbDirectory           = rtrim($dbDirectory, '/\\');
		$this->generated             = [];
		$this->outputSizes           = [];
		$this->mimeTypes             = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
		$this->allowedExtensions     = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
		$this->safePathName          = Str::random(64);
		$this->defaultWatermark      = "{$this->publicDirectory}/images/watermark/watermark.png";
		$this->watermarkBottomMargin = 30;
		$this->watermarkRightMargin  = 30;
		$this->validOptions          = [
			'useLongerDimension', 'resampleMethod', 'jpgCompression',
			'pngCompression', 'separateByYear', 'separateByMonth',
			'defaultThumbnailSize', 'watermark', 'doNotWatermark',
		];
		
		$this->useLongerDimension();
		$this->resampleMethod();
		$this->jpgCompression(null);
		$this->pngCompression(null);
		$this->separateByMonth(null);
		$this->separateByYear(null);
		$this->defaultThumbnailSize(null);
	}
	
	/**
	 * @param  UploadedFile|File  $image
	 * @return $this
	 */
	public function setImage($image): self
	{
		if (! $image instanceof File || ! $image instanceof UploadedFile) {
			
			throw new InvalidArgumentException('Only types class instances "File" or "UploadedFile" accepted!');
		}
		
		$this->image = $image;
		// Mime must be present and valid for resource generation.
		$this->mime();
		
		$this->resource = $this->createResource();
		
		$this->extension();
		$this->size();
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function mime(): self
	{
		$this->hasImage();
		
		$this->mime = $this->image->getMimeType();
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function extension(): self
	{
		$this->hasImage();
		
		$this->extension = $this->image->guessExtension() ?? $this->image->getExtension();
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function size(): self
	{
		$this->hasImage();
		
		$this->size['width']  = imagesx($this->resource);
		$this->size['height'] = imagesy($this->resource);
		
		return $this;
	}
	
	/**
	 * @param  array  $outputSizes
	 * @return $this
	 */
	public function setOutputSizes(?array $outputSizes): self
	{
		foreach ($outputSizes as $size) {
			if (! $this->isNumeric($size) || $size < 0) {
				throw new InvalidArgumentException(
					sprintf(
						'Sizes must be a positive integer type. Value "%s" does not conform.',
						$size
					)
				);
			}
			$this->outputSizes[] = $size;
		}
		
		return $this;
	}
	
	/**
	 * @param  string  $destination
	 * @return $this
	 */
	public function destination(string $destination): self
	{
		$this->destination = $this->uploadDirectory . '/' . str_replace('/', '', str_replace('\\', '', $destination));
		$this->dbDirectory = $this->dbDirectory . '/' . str_replace('/', '', str_replace('\\', '', $destination));
		
		return $this;
	}
	
	/**
	 * @param  bool  $useLongerDimension
	 * @return $this
	 */
	public function useLongerDimension(bool $useLongerDimension = true): self
	{
		$this->useLongerDimension = $useLongerDimension;
		
		return $this;
	}
	
	/**
	 * @param  int|string  $method
	 * @return $this
	 */
	public function resampleMethod(string $method = IMG_BILINEAR_FIXED): self
	{
		switch (strtolower($method)) {
			case 'bicubic':
				$this->resample = IMG_BICUBIC;
				break;
			case 'bicubic-fixed':
				$this->resample = IMG_BICUBIC_FIXED;
				break;
			case 'nearest-neighbour':
			case 'nearest-neighbor':
				$this->resample = IMG_NEAREST_NEIGHBOUR;
				break;
			default:
				$this->resample = IMG_BILINEAR_FIXED;
		}
		
		return $this;
	}
	
	/**
	 * @param  null|int  $compression
	 * @return $this
	 */
	public function jpgCompression(?int $compression): self
	{
		$this->jpgCompression = $compression ?? 85;
		
		return $this;
	}
	
	/**
	 * @param  null|int  $compression
	 * @return $this
	 */
	public function pngCompression(?int $compression): self
	{
		$this->pngCompression = $compression ?? 5;
		
		return $this;
	}
	
	/**
	 * @param  null|File  $watermark
	 * @return $this
	 */
	public function watermark(?File $watermark): self
	{
		if (null === $watermark) {
			
			$this->defaultWatermark();
			
			$this->hasDefaultWatermark();
		} else {
			
			$this->watermark = $watermark;
		}
		
		$this->watermarkMime();
		
		$this->watermarkResource = $this->createWatermarkResource();
		
		$this->watermarkSize();
		
		// Make sure our image is set for watermarking.
		$this->watermarkImage = true;
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function defaultWatermark(): self
	{
		$this->watermark = new File($this->defaultWatermark);
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function doNotWatermark(): self
	{
		$this->watermark      = null;
		$this->watermarkImage = false;
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function watermarkMime(): self
	{
		$this->watermarkMime = $this->watermark->getMimeType();
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function watermarkSize(): self
	{
		$this->watermarkSize['width']  = imagesx($this->watermarkResource);
		$this->watermarkSize['height'] = imagesy($this->watermarkResource);
		
		return $this;
	}
	
	/**
	 * @param  null|bool  $separateByMonth
	 * @return $this
	 */
	public function separateByMonth(?bool $separateByMonth): self
	{
		$this->separateByMonth = $separateByMonth ?? true;
		
		return $this;
	}
	
	/**
	 * @param  null|bool  $separateByYear
	 * @return $this
	 */
	public function separateByYear(?bool $separateByYear): self
	{
		$this->separateByYear = $separateByYear ?? true;
		
		return $this;
	}
	
	/**
	 * @param  null|int  $size
	 * @return $this
	 */
	public function defaultThumbnailSize(?int $size): self
	{
		$this->defaultThumbnailSize = $size ?? 150;
		
		return $this;
	}
	
	/**
	 * @param  array  $options
	 */
	public function setOptions(array $options): void
	{
		foreach ($options as $option => $value) {
			
			if ($this->isNumeric($option)) {
				
				throw new InvalidArgumentException(
					sprintf(
						'Expected key => value pair with string as key, got numeric value instead.%s
						 "%s" => "%s". Options must be an associative array.',
						PHP_EOL, (string) $option, (string) $value
					)
				);
			}
			
			if (! $this->inArray($option, $this->validOptions)) {
				
				throw new InvalidArgumentException(
					sprintf(
						'Invalid option in the options array passed to setOptions(). %s
						"%s" is not a valid option.%s
						 Expected keys - "%s".',
						PHP_EOL, (string) $option, PHP_EOL, implode(', ', $this->validOptions)
					)
				);
			}
			
			if (method_exists($this, $option)) {
				
				$this->$option($value);
			}
		}
	}
	
	/**
	 * @param  UploadedFile|File  $image
	 * @param  string             $destination
	 * @param  null|int[]         $sizes
	 * @return array
	 * @throws Exception
	 */
	public function processAndSave($image, string $destination, ?array $sizes): array
	{
		$this->setImage($image);
		
		$this->destination($destination);
		
		if (false === $this->isset($this->outputSizes) && null !== $sizes) {
			$this->setOutputSizes($sizes);
		}
		
		if ($this->useLongerDimension
			&& $this->size['height'] > $this->size['width']) {
			$this->recalculateOutput();
		}
		
		if (true === $this->watermarkImage) {
			if (! $this->markOriginalResource()) {
				
				throw new Exception(
					sprintf(
						'Error when watermarking file "%s" with watermark "%s".',
						$this->image->getPathname(), $this->watermark->getPathname()
					)
				);
			}
		}
		
		$this->formatFinalPaths();
		
		// Final integrity check
		if (false === $this->isValid) {
			$this->integrity();
		}
		
		// Add the original size as the first element by default
		// todo - this is a patch fix for portraits to use the longer dimension for. Test thorough!!!
		$this->currentWorkingSize = $this->size['width'] > $this->size['height']
			? $this->size['width'] > 900 ? 900 : $this->size['width']
			: $this->size['height'] > 900 ? 900 : $this->size['height'];
		
		$this->jpgCompression(60);
		$this->generated['original'] = $this->save('original');
		
		// If output sizes are not set, leave the default options only.
		if ($this->isset($this->outputSizes)) {
			
			foreach ($this->outputSizes as $size) {
				
				if ($size >= $this->size['width']) {
					continue;
				}
				
				$this->currentWorkingSize = $size;
				$this->generated[$size]   = $this->save();
			}
		}
		
		// Add a thumbnail size as the second element by default
		$this->currentWorkingSize = $this->defaultThumbnailSize;
		$this->jpgCompression(50);
		$this->pngCompression(0);
		$this->generated['thumbnail'] = $this->save('thumbnail');
		
		// Add a placeholder image at low low quality at the end
		$this->currentWorkingSize = $this->size['width'] / 2;
		$this->jpgCompression(0);
		$this->pngCompression(0);
		$this->generated['thumb_placeholder'] = $this->save('thumb_placeholder');
		
		// Add a placeholder image at low low quality at the end
		$this->currentWorkingSize = $this->size['width'] / 2;
		$this->jpgCompression(0);
		$this->pngCompression(0);
		$this->generated['placeholder'] = $this->save('placeholder');
		
		imagedestroy($this->resource);
		
		return $this->generated;
	}
	
	/**
	 * @param  string  $addToName
	 * @return ResizedImage
	 * @throws Exception
	 */
	public function save(string $addToName = ''): ResizedImage
	{
		$this->currentWorkingResource = $this->createScaledResource();
		$this->currentWorkingName     = $this->newFilename($addToName);
		
		$this->currentWorkingPath = "{$this->destination}/{$this->currentWorkingName}";
		
		if ($this->areWorkingPropertiesEmpty()) {
			
			throw new Exception('WORKING PROPERTIES NOT SET');
		}
		
		$result = $this->write();
		
		if (true === $result) {
			$dbPath = "{$this->dbDirectory}/{$this->currentWorkingName}";
			
			imagedestroy($this->currentWorkingResource);
			
			$image = new ResizedImage($this->currentWorkingSize, $dbPath, $this->currentWorkingPath);
			
			$this->resetCurrentWorkingProperties();
			
			return $image;
		} else {
			
			throw new Exception('');
		}
	}
	
	/**
	 * @return bool
	 * @throws Exception
	 */
	public function write(): bool
	{
		switch ($this->mime) {
			case 'image/jpeg':
				return imagejpeg($this->currentWorkingResource, $this->currentWorkingPath, $this->jpgCompression);
			case 'image/png':
				return imagepng($this->currentWorkingResource, $this->currentWorkingPath, $this->pngCompression);
			case 'image/gif':
				return imagegif($this->currentWorkingResource, $this->currentWorkingPath);
			case 'image/webp':
				return imagewebp($this->currentWorkingResource, $this->currentWorkingPath);
			default:
				throw new Exception(
					sprintf(
						'Error in makeImage() method!!!! Should not have made it here. Mime type is invalid. "%s"',
						$this->mime
					)
				);
		}
	}
	
	/**
	 * @return bool
	 */
	public function markOriginalResource(): bool
	{
		// If the watermark is not passed in and is not set, attempt to set the default watermark.
		if (false === $this->isWatermark()) {
			$this->watermark(null);
		}
		
		$x = $this->size['width'] - $this->watermarkSize['width'] - $this->watermarkBottomMargin;
		$y = $this->size['height'] - $this->watermarkSize['height'] - $this->watermarkRightMargin;
		
		return imagecopy($this->resource, $this->watermarkResource, $x, $y, 0, 0, $this->watermarkSize['width'], $this->watermarkSize['height']);
	}
	
	/**
	 * @param  string  $addToName
	 * @return string
	 */
	public function newFilename(string $addToName = ''): string
	{
		if ($this->isset($addToName)) {
			return "{$this->safePathName}_{$addToName}.{$this->extension}";
		}
		
		return "{$this->safePathName}_{$addToName}.{$this->extension}";
	}
	
	/**
	 * @return string
	 */
	public function formatFinalPaths(): string
	{
		if ($this->separateByYear) {
			$this->destination .= '/' . strtolower(date('Y', time()));
			$this->dbDirectory .= '/' . strtolower(date('Y', time()));
		}
		
		if (false === $this->isDestinationDirectory()) {
			mkdir($this->destination, 0755, true);
		}
		
		if ($this->separateByMonth) {
			$this->destination .= '/' . strtolower(date('F', time()));
			$this->dbDirectory .= '/' . strtolower(date('F', time()));
		}
		
		if (false === $this->isDestinationDirectory()) {
			mkdir($this->destination, 0755, true);
		}
		
		return $this->destination;
	}
	
	/**
	 * Recalculate the sizes to be inline with the longer dimension of the image resource.
	 */
	public function recalculateOutput(): void
	{
		$w = imagesx($this->resource);
		$h = imagesy($this->resource);
		
		foreach ($this->outputSizes as &$size) {
			$size = round($size * $w / $h, -1);
		}
	}
	
	/**
	 * @return bool|resource
	 */
	public function createScaledResource()
	{
		return imagescale($this->resource, $this->currentWorkingSize, -1, $this->resample);
	}
	
	/**
	 * @return bool|resource
	 */
	public function createResource()
	{
		$this->hasImage();
		
		$this->hasMime();
		
		switch ($this->mime) {
			case 'image/jpeg':
				return imagecreatefromjpeg($this->image->getPathname());
				break;
			case 'image/png':
				return imagecreatefrompng($this->image->getPathname());
				break;
			case 'image/gif':
				return imagecreatefromgif($this->image->getPathname());
				break;
			case 'image/webp':
				return imagecreatefromwebp($this->image->getPathname());
				break;
			default:
				return false;
		}
	}
	
	/**
	 * @return bool|resource
	 */
	public function createWatermarkResource()
	{
		switch ($this->watermarkMime) {
			case 'image/jpeg':
				return imagecreatefromjpeg($this->watermark->getPathname());
				break;
			case 'image/png':
				return imagecreatefrompng($this->watermark->getPathname());
				break;
			case 'image/gif':
				return imagecreatefromgif($this->watermark->getPathname());
				break;
			case 'image/webp':
				return imagecreatefromwebp($this->watermark->getPathname());
				break;
			default:
				return false;
		}
	}
	
	/**
	 * Verify object integrity. Check all possible properties for validity.
	 * Default values are set for many for the purpose of validating them always.
	 * Invalid properties can only occur only if invalid values are set by the user.
	 */
	public function integrity(): void
	{
		if (
			$this->hasDirectories()
			&& $this->hasImage()
			&& $this->hasResource()
			&& $this->hasMime()
			&& $this->hasSize()
			&& $this->hasExtension()
			&& $this->hasWatermark()
			&& $this->hasJpgCompression()
			&& $this->hasPngCompression()
		) {
			$this->isValid = true;
		}
		
		$this->isValid = false;
	}
	
	/**
	 * @return bool
	 */
	public function hasDirectories(): bool
	{
		if (false === $this->isDir($this->uploadDirectory)
			|| false === $this->isWritable($this->uploadDirectory)) {
			
			$this->errors['uploadDirectory'][] = sprintf(
				'Upload directory "%s" does not exist or is not writable.',
				$this->uploadDirectory
			);
		}
		
		$dbFullDir = "{$this->publicDirectory}/{$this->dbDirectory}";
		
		if (false === $this->isDir($dbFullDir)
			|| false === $this->isWritable($dbFullDir)) {
			
			$this->errors['databaseDirectory'][] = sprintf(
				'Database directory "%s" does not exist or is not writable.',
				$dbFullDir
			);
		}
		
		if (false === $this->isDestinationDirectory()) {
			
			$this->errors['destination'][] = sprintf(
				'Destination directory "%s" does not exist or is not writable.',
				$this->destination
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function isDestinationDirectory(): bool
	{
		return $this->isDir($this->destination)
			|| $this->isWritable($this->destination)
			|| $this->fileExists($this->destination);
	}
	
	/**
	 * @return bool
	 */
	public function hasImage(): bool
	{
		if (false === $this->isImage()) {
			
			$this->errors['image'][] = 'No image file is set. Please use the setImage() method.';
		}
		
		if (false === $this->isImageFile()) {
			
			$this->errors['image'][] = sprintf(
				'No image file can be located or file is not readable. Expected location - "%s".',
				$this->image->getPathname() ?? null
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function isImage(): bool
	{
		return $this->isset($this->image);
	}
	
	/**
	 * @return bool
	 */
	public function isImageFile(): bool
	{
		return $this->fileExists($this->image->getPathname())
			&& $this->fileReadable($this->image->getPathname());
	}
	
	/**
	 * @return bool
	 */
	public function hasResource(): bool
	{
		if (false === $this->isset($this->resource)
			|| false === $this->resource) {
			
			$this->errors['resource'][] = sprintf(
				'Property "resource" is not set or is invalid. Expected image resource, actual "%s".',
				(string) $this->resource
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function hasWatermark(): bool
	{
		// Return if image does not need a watermark. Default is set to true.
		if (false === $this->watermarkImage) {
			
			return true;
		}
		
		if (false === $this->isWatermark()) {
			
			$this->errors['watermark'][] = 'Watermark property is not set.';
		}
		
		if (false === $this->isWatermarkFile()) {
			
			$this->errors['watermark'][] = sprintf(
				'Watermark file - "%s" does not exist.',
				$this->watermark->getPathname()
			);
		}
		
		if (false === $this->isWatermarkFileReadable()) {
			
			$this->errors['watermark'][] = sprintf(
				'Watermark file - "%s" is not readable.',
				$this->watermark->getPathname()
			);
		}
		
		if (false === $this->isWatermarkMime()) {
			
			$this->errors['watermark'][] = sprintf(
				'Watermark mime "%s" is not a valid type.%s
				Expected - "%s"',
				$this->watermarkMime, PHP_EOL, implode(', ', $this->mimeTypes)
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function isWatermark(): bool
	{
		return $this->isset($this->watermark);
	}
	
	/**
	 * @return bool
	 */
	public function isWatermarkFile(): bool
	{
		return $this->fileExists($this->watermark->getPathname() ?? null);
	}
	
	/**
	 * @return bool
	 */
	public function isWatermarkFileReadable(): bool
	{
		return $this->fileReadable($this->watermark->getPathname() ?? null);
	}
	
	/**
	 * @return bool
	 */
	public function isWatermarkMime(): bool
	{
		return $this->inArray($this->watermarkMime, $this->mimeTypes);
	}
	
	/**
	 * Verify the default watermark exists and is readable only.
	 * It will then be set as watermark and verified later on in the process.
	 * @return bool
	 */
	public function hasDefaultWatermark(): bool
	{
		if (false === $this->isDefaultWatermarkFile()) {
			
			$this->errors['watermark'][] = sprintf(
				'Default watermark in location "%s" does not exist.',
				$this->defaultWatermark
			);
		}
		
		if (false === $this->isDefaultWatermarkFileReadable()) {
			
			$this->errors['watermark'][] = sprintf(
				'Default watermark in location "%s" is not readable.',
				$this->defaultWatermark
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function isDefaultWatermarkFile(): bool
	{
		return $this->fileExists($this->defaultWatermark);
	}
	
	/**
	 * @return bool
	 */
	public function isDefaultWatermarkFileReadable(): bool
	{
		return $this->fileReadable($this->defaultWatermark);
	}
	
	/**
	 * @return bool
	 */
	public function hasJpgCompression(): bool
	{
		if ($this->jpgCompression < 0 || $this->jpgCompression > 100) {
			
			$this->errors['jpgCompression'][] = sprintf(
				'Property "jpgCompression" expected from 0 to 100, actual "%d".',
				$this->jpgCompression
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function hasPngCompression(): bool
	{
		if ($this->pngCompression < 0 || $this->pngCompression > 9) {
			
			$this->errors['pngCompression'][] = sprintf(
				'Property "pngCompression" expected from 0 to 9, actual "%d".',
				$this->pngCompression
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function hasMime(): bool
	{
		if (! $this->isMime()) {
			
			$this->errors['mime'][] = 'Property "mime" is not set.';
		}
		
		if (! $this->isMimeType()) {
			
			$this->errors['mime'][] = sprintf(
				'Mime type "%s" is not a valid mime, expected "%s".',
				$this->mime, implode(', ', $this->mimeTypes)
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function isMime(): bool
	{
		return $this->isset($this->mime);
	}
	
	/**
	 * @return bool
	 */
	public function isMimeType(): bool
	{
		return $this->inArray($this->mime, $this->mimeTypes);
	}
	
	/**
	 * @return bool
	 */
	public function hasSize(): bool
	{
		if (false === $this->isSize()) {
			
			$this->errors['size'][] = 'Property "size" is not set.';
		}
		
		if (false === $this->isSizeKeys()) {
			
			$this->errors['size'][] = 'Keys "height" and "width" are not properly set.';
		}
		
		if (false === $this->isSizeValues()) {
			
			$this->errors['size'][] = sprintf(
				'Values for "height"" and "width" on "size" property must be larger than 0 and positive integers. \n
			"height" - "%s" and "width" - "%s" encountered.',
				(string) $this->size['height'], (string) $this->size['width']
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function isSize(): bool
	{
		return $this->isset($this->size);
	}
	
	/**
	 * @return bool
	 */
	public function isSizeKeys(): bool
	{
		return $this->isset($this->size['height']) || $this->isset($this->size['width']);
	}
	
	/**
	 * @return bool
	 */
	public function isSizeValues(): bool
	{
		return $this->size['height'] > 1 || $this->size['width'] > 1;
	}
	
	/**
	 * @return bool
	 */
	public function hasExtension(): bool
	{
		if (false === $this->isExtension()) {
			
			$this->errors['extension'][] = 'File extension is not set.';
		}
		
		if (false === $this->isExtensionType()) {
			
			$this->errors['extension'][] = sprintf(
				'File extension "%s" is not a valid extension.%s
				Expected - "%s"',
				$this->extension, PHP_EOL, implode(', ', $this->allowedExtensions)
			);
		}
		
		if (true === $this->errorCount()) {
			
			$this->generateErrorReport();
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function isExtension(): bool
	{
		return $this->isset($this->extension);
	}
	
	/**
	 * @return bool
	 */
	public function isExtensionType(): bool
	{
		return $this->inArray($this->extension, $this->allowedExtensions);
	}
	
	/**
	 * @param $var
	 * @return bool
	 */
	public function isset($var): bool
	{
		return isset($var) && ! empty($var) || false === $var;
	}
	
	/**
	 * @param $var
	 * @return bool
	 */
	public function isNumeric($var): bool
	{
		return is_integer($var) || is_numeric($var) && ! is_string($var);
	}
	
	/**
	 * @param  string  $filePathName
	 * @return bool
	 */
	public function isDir(string $filePathName): bool
	{
		return is_dir($filePathName);
	}
	
	/**
	 * @param  string  $filePathName
	 * @return bool
	 */
	public function isWritable(string $filePathName): bool
	{
		return is_writable($filePathName);
	}
	
	/**
	 * @param  string  $filePathName
	 * @return bool
	 */
	public function fileExists(string $filePathName): bool
	{
		return file_exists($filePathName);
	}
	
	/**
	 * @param  string  $filePathName
	 * @return bool
	 */
	public function fileReadable(string $filePathName): bool
	{
		return is_readable($filePathName);
	}
	
	/**
	 * @param $needle
	 * @param $haystack
	 * @return bool
	 */
	public function inArray($needle, $haystack): bool
	{
		return in_array($needle, $haystack);
	}
	
	/**
	 * @return bool
	 */
	public function errorCount(): bool
	{
		return count($this->errors) > 0;
	}
	
	/**
	 * Generate and show errors.
	 */
	public function generateErrorReport(): void
	{
		foreach ($this->errors as $error) {
			
			foreach ($error as $type => $message) {
				
				echo "{$type} - {$message}";
			}
		}
		
		die;
	}
	
	/**
	 * Reset working properties for the next size to be set.
	 */
	public function resetCurrentWorkingProperties(): void
	{
		$this->currentWorkingSize     = null;
		$this->currentWorkingPath     = null;
		$this->currentWorkingResource = null;
		$this->currentWorkingName     = null;
	}
	
	/**
	 * @return bool
	 */
	public function areWorkingPropertiesEmpty(): bool
	{
		if (false === $this->isset($this->currentWorkingSize)
			&& false === $this->isset($this->currentWorkingPath)
			&& false === $this->isset($this->currentWorkingResource)
			&& false === $this->isset($this->currentWorkingName)) {
			
			return true;
		}
		
		return false;
	}
}