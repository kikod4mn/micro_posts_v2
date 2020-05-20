<?php

declare(strict_types = 1);

namespace App\Service\Concerns;

use App\Service\Contracts\ImageUploaderInterface;
use App\Support\Str;
use App\Support\Validate;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Throwable;

trait UploadsImages
{
	/**
	 * @return null|File
	 */
	public function getImage(): ?File
	{
		return null;
	}
	
	/**
	 * @param  File  $file
	 * @return $this|ImageUploaderInterface
	 */
	public function setImage(File $file): ImageUploaderInterface
	{
		$this->image = $file;
		// Set a new fresh filename.
		$this->finalName()
			// Validate the image mime.
			 ->validMime()
			// Create a resource based on the image
			 ->createResource()
			// Get image size info.
			 ->size()
		;
		
		return $this;
	}
	
	/**
	 * @return $this|ImageUploaderInterface
	 */
	private function validMime(): ImageUploaderInterface
	{
		$this->mime = $this->image->getMimeType();
		
		return $this;
	}
	
	/**
	 * @return $this|ImageUploaderInterface
	 */
	private function createResource(): ImageUploaderInterface
	{
		if (! Validate::isSet($this->mime)) {
			throw new InvalidArgumentException(
				'"mime" property is not set. Invalid image file or unable to guess mime for "%s"', escape($this->getImage()->getFilename())
			);
		}
		
		switch ($this->mime) {
			case 'image/jpeg':
				$this->resource = imagecreatefromjpeg($this->image->getPathname());
				break;
			case 'image/png':
				$this->resource = imagecreatefrompng($this->image->getPathname());
				break;
			case 'image/gif':
				$this->resource = imagecreatefromgif($this->image->getPathname());
				break;
			case 'image/webp':
				$this->resource = imagecreatefromwebp($this->image->getPathname());
				break;
			default:
				$this->resource = null;
		}
		
		return $this;
	}
	
	/**
	 * @return bool|resource
	 */
	public function createWatermarkResource()
	{
		switch ($this->watermark->getMimeType()) {
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
	
	public function hasWatermark(): bool
	{
		if (! Validate::isSet($this->watermark)) {
			$this->watermark = $this->defaultWatermark;
		}
		
		return Validate::isSet($this->watermark);
	}
	
	/**
	 * @param  bool  $separateByMonth
	 * @return $this|ImageUploaderInterface
	 */
	public function separateByMonth(bool $separateByMonth = true): ImageUploaderInterface
	{
		$this->separateByMonth = $separateByMonth;
		
		return $this;
	}
	
	/**
	 * @param  bool  $separateByYear
	 * @return $this|ImageUploaderInterface
	 */
	public function separateByYear(bool $separateByYear): ImageUploaderInterface
	{
		$this->separateByYear = $separateByYear;
		
		return $this;
	}
	
	private function size(): self
	{
		$this->size['width']  = imagesx($this->resource);
		$this->size['height'] = imagesy($this->resource);
		
		return $this;
	}
	
	/**
	 * @return null|File
	 */
	public function getWatermark(): ?File
	{
		return $this->watermark;
	}
	
	/**
	 * @param  File  $file
	 * @return $this|ImageUploaderInterface
	 */
	public function setWatermark(File $file): ImageUploaderInterface
	{
		$this->watermark = $file;
		
		return $this;
	}
	
	/**
	 * @return null|array
	 */
	public function getOutputSizes(): ?array
	{
		return $this->outputSizes;
	}
	
	/**
	 * @param  int[]  $sizes
	 * @return $this|ImageUploaderInterface
	 */
	public function setOutputSizes(array $sizes): ImageUploaderInterface
	{
		foreach ($sizes as $size) {
			if (! Validate::isNumeric($size) || $size < 0) {
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
	 * @return null|int
	 */
	public function getReSampleMethod(): ?int
	{
		return $this->reSampleMethod;
	}
	
	/**
	 * @param  int  $method
	 * @return $this|ImageUploaderInterface
	 */
	public function setReSampleMethod(int $method = IMG_BILINEAR_FIXED): ImageUploaderInterface
	{
		switch (strtolower($method)) {
			case 'bicubic':
				$this->reSampleMethod = IMG_BICUBIC;
				break;
			case 'bicubic-fixed':
				$this->reSampleMethod = IMG_BICUBIC_FIXED;
				break;
			case 'nearest-neighbour':
			case 'nearest-neighbor':
				$this->reSampleMethod = IMG_NEAREST_NEIGHBOUR;
				break;
			default:
				$this->reSampleMethod = IMG_BILINEAR_FIXED;
		}
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getDefaultThumbnailSize(): int
	{
		return $this->defaultThumbnailSize;
	}
	
	/**
	 * @param  int  $default
	 * @return $this|ImageUploaderInterface
	 */
	public function setDefaultThumbnailSize(int $default = 250): ImageUploaderInterface
	{
		$this->defaultThumbnailSize = $default;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getJpgCompression(): int
	{
		return $this->jpgCompression;
	}
	
	/**
	 * @param  int  $compression
	 * @return $this|ImageUploaderInterface
	 */
	public function setJpgCompression(int $compression = 60): ImageUploaderInterface
	{
		if ($compression < 0 || $compression > 100) {
			
			throw new InvalidArgumentException(
				sprintf('Argument "$compression" must be an integer between 0 and 100. "%d" is not valid.', $compression)
			);
		}
		
		$this->jpgCompression = $compression;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getPngCompression(): int
	{
		return $this->pngCompression;
	}
	
	/**
	 * @param  int  $compression
	 * @return $this|ImageUploaderInterface
	 */
	public function setPngCompression(int $compression = 5): ImageUploaderInterface
	{
		if ($compression < 0 || $compression > 9) {
			
			throw new InvalidArgumentException(
				sprintf('Argument "$compression" must be an integer between 0 and 9. "%d" is not valid.', $compression)
			);
		}
		
		$this->pngCompression = $compression;
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getAllowedTypes(): array
	{
		return defined('static::DEFAULT_ALLOWED_TYPES') ? array_merge(static::DEFAULT_ALLOWED_TYPES, $this->allowedTypes) : $this->allowedTypes;
	}
	
	/**
	 * @return array
	 */
	public function getAllowedExts(): array
	{
		return defined('static::DEFAULT_ALLOWED_EXTS') ? array_merge(static::DEFAULT_ALLOWED_EXTS, $this->allowedExts) : $this->allowedExts;
	}
	
	/**
	 * @return string
	 */
	public function getDestination(): string
	{
		return $this->destination;
	}
	
	/**
	 * @param  string  $destination
	 * @return $this|ImageUploaderInterface
	 */
	public function setDestination(string $destination): ImageUploaderInterface
	{
		$this->destination =
			rtrim(
				$this->parameterBag->get('kernel.project_dir'), '\\/'
			) .
			'/public/' .
			ltrim(
				rtrim($destination, '\\/'),
				'\\='
			);
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getFinalName(): string
	{
		return $this->finalName;
	}
	
	/**
	 * Append the size or identification string to the name after double underscore by default
	 * @param  string  $name
	 * @param  string  $addition
	 * @param  string  $after
	 * @return string
	 */
	private function appendToName(string $name, string $addition, string $after = '__'): string
	{
		return Str::beforeLast($name, $after) . "__{$addition}" . Str::afterLast($name, $after);
	}
	
	/**
	 * @param  string  $append
	 * @return $this|ImageUploaderInterface
	 */
	private function finalName(string $append = '__'): ?ImageUploaderInterface
	{
		$ext = $this->getImage()->guessExtension();
		
		if (! $ext) {
			throw new InvalidArgumentException('Cannot guess the file\'s extension. Please provide a valid file.');
		}
		
		try {
			$this->finalName = Str::random(32) . "{$append}.{$ext}";
		} catch (Throwable $e) {
			return null;
		}
		
		return $this;
	}
}