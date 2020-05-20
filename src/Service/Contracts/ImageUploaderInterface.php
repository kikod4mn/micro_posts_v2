<?php

declare(strict_types = 1);

namespace App\Service\Contracts;

use Symfony\Component\HttpFoundation\File\File;

interface ImageUploaderInterface
{
	/**
	 * @return array
	 */
	public function resize(): array;
	
	/**
	 * @param  null|File  $file
	 * @return $this|ImageUploaderInterface
	 */
	public function process(?File $file): ImageUploaderInterface;
	
	/**
	 * @return null|File
	 */
	public function getImage(): ?File;
	
	/**
	 * @param  File  $file
	 * @return $this|ImageUploaderInterface
	 */
	public function setImage(File $file): ImageUploaderInterface;
	
	/**
	 * @return null|File
	 */
	public function getWatermark(): ?File;
	
	/**
	 * @param  File  $file
	 * @return $this|ImageUploaderInterface
	 */
	public function setWatermark(File $file): ImageUploaderInterface;
	
	/**
	 * @return null|array
	 */
	public function getOutputSizes(): ?array;
	
	/**
	 * @param  int[]  $sizes
	 * @return $this|ImageUploaderInterface
	 */
	public function setOutputSizes(array $sizes): ImageUploaderInterface;
	
	/**
	 * @return int
	 */
	public function getDefaultThumbnailSize(): int;
	
	/**
	 * @param  int  $default
	 * @return $this|ImageUploaderInterface
	 */
	public function setDefaultThumbnailSize(int $default = 250): ImageUploaderInterface;
	
	/**
	 * @return int
	 */
	public function getJpgCompression(): int;
	
	/**
	 * @param  int  $compression
	 * @return $this|ImageUploaderInterface
	 */
	public function setJpgCompression(int $compression = 60): ImageUploaderInterface;
	
	/**
	 * @return int
	 */
	public function getPngCompression(): int;
	
	/**
	 * @param  int  $compression
	 * @return $this|ImageUploaderInterface
	 */
	public function setPngCompression(int $compression = 5): ImageUploaderInterface;
	
	/**
	 * @return array
	 */
	public function getAllowedTypes(): array;
	
	/**
	 * @return array
	 */
	public function getAllowedExts(): array;
	
	/**
	 * @return string
	 */
	public function getFinalName(): string;
}