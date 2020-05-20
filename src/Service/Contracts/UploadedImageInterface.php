<?php

declare(strict_types = 1);

namespace App\Service\Contracts;

interface UploadedImageInterface
{
	public function getPlaceholder(): ?string;
	
	public function setPlaceholder(string $placeholder): UploadedImageInterface;
	
	public function getThumbnailPlaceholder(): ?string;
	
	public function setThumbnailPlaceholder(string $placeholder): UploadedImageInterface;
	
	public function getThumbnail(): ?string;
	
	public function setThumbnail(string $thumbnail): UploadedImageInterface;
	
	public function getOriginal(): ?string;
	
	public function setOriginal(string $original): UploadedImageInterface;
	
	public function getFilePath(): ?string;
	
	public function setFilePath(string $path): UploadedImageInterface;
}