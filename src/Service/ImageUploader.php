<?php

declare(strict_types = 1);

namespace App\Service;

use App\Service\Concerns\UploadsImages;
use App\Service\Contracts\ImageUploaderInterface;
use App\Support\Validate;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;

class ImageUploader implements ImageUploaderInterface
{
	use UploadsImages;
	
	const DEFAULT_ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
	
	const DEFAULT_ALLOWED_EXTS  = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
	
	/**
	 * @var File
	 */
	private $image = null;
	
	/**
	 * @var string
	 */
	private $mime = '';
	
	/**
	 * @var File
	 */
	private $watermark = null;
	
	/**
	 * @var int
	 */
	private $defaultThumbnailSize = 250;
	
	/**
	 * @var string[]
	 */
	private $allowedExts = [];
	
	/**
	 * @var string[]
	 */
	private $allowedTypes = [];
	
	/**
	 * @var int
	 */
	private $pngCompression = 5;
	
	/**
	 * @var int
	 */
	private $jpgCompression = 60;
	
	/**
	 * @var int[]
	 */
	private $outputSizes = [];
	
	/**
	 * @var string
	 */
	private $finalName = '';
	
	/**
	 * @var bool
	 */
	private $addWatermark = false;
	
	/**
	 * @var string
	 */
	private $destination = '';
	
	/**
	 * @var null|File
	 */
	private $defaultWatermark = null;
	
	/**
	 * @var int
	 */
	private $reSampleMethod = IMG_BILINEAR_FIXED;
	
	/**
	 * @var resource
	 */
	private $resource;
	
	/**
	 * @var bool
	 */
	private $separateByMonth = true;
	
	/**
	 * @var bool
	 */
	private $separateByYear = true;
	
	/**
	 * @var ParameterBagInterface
	 */
	private $parameterBag;
	
	/**
	 * ImageUploader constructor.
	 * @param  ParameterBagInterface  $parameterBag
	 */
	public function __construct(ParameterBagInterface $parameterBag)
	{
		$defaultMark = rtrim($this->parameterBag->get('kernel.project_dir'), '\\/') . '/public/images/watermark/watermark.png';
		Validate::isFileOk($defaultMark) ? $this->defaultWatermark = new File($defaultMark) : null;
	}
	
	/**
	 * @param  bool  $mark
	 * @return $this|ImageUploaderInterface
	 */
	public function markImage(bool $mark = false): self
	{
		$this->addWatermark = $mark;
		
		return $this;
	}
	
	public function resize(): array
	{
		return [];
	}
	
	/**
	 * @param  null|File  $file
	 * @return $this|ImageUploaderInterface
	 */
	public function process(?File $file): ImageUploaderInterface
	{
		if (! is_null($file)) {
			$this->setImage($file);
		}
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function save(): array
	{
		return [];
	}
}