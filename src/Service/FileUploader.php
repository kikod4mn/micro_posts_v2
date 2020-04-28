<?php

namespace App\Service;

use App\Kikopolis\Str;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
	/** @var string */
	private $targetDirectory;
	
	/** @var LoggerInterface */
	private $logger;
	
	/** @var bool */
	private $separatedByMonth;
	
	/** @var string */
	private $dbDirectory;
	
	/**
	 * FileUploader constructor.
	 * @param  string           $uploadDirectory
	 * @param  string           $dbDirectory
	 * @param  LoggerInterface  $logger
	 */
	public function __construct(string $uploadDirectory, string $dbDirectory, LoggerInterface $logger)
	{
		$this->targetDirectory  = $uploadDirectory;
		$this->logger           = $logger;
		$this->separatedByMonth = true;
		$this->dbDirectory      = $dbDirectory;
	}
	
	/**
	 * Upload a file, rename it and move to the specified upload dir.
	 * If you wish to add to the default target directory, use method addToTargetDir before the upload call.
	 * If you wish to separate uploads by month, do nothing, leave the second parameter as is.
	 * @param  UploadedFile  $file
	 * @return string
	 * @throws Exception
	 */
	public function upload(UploadedFile $file): string
	{
		if ($this->separatedByMonth) {
			$this->targetDirectory .= $this->addCurrentMonth();
			$this->dbDirectory     .= $this->addCurrentMonth();
		}
		$safeFileName = Str::random(32) . '-' . uniqid() . '.' . $file->guessExtension();
		try {
			$file->move($this->targetDirectory, $safeFileName);
		} catch (FileException $fileException) {
			$this->logger->error($fileException);
		}
		
		return $this->dbDirectory . $safeFileName;
	}
	
	/**
	 * @return string
	 */
	public function getTargetDirectory(): string
	{
		return $this->targetDirectory;
	}
	
	/**
	 * Add to the default target directory.
	 * Do not call this if you only wish to separate uploads by month as that is the default behaviour.
	 * @param  string  $targetDirectory
	 */
	public function addToTargetDirectory(string $targetDirectory): void
	{
		$this->targetDirectory .= strtolower($targetDirectory);
		$this->dbDirectory     .= strtolower($targetDirectory);
	}
	
	/**
	 * @return bool
	 */
	public function isSeparatedByMonth(): bool
	{
		return $this->separatedByMonth;
	}
	
	/**
	 * @param  bool  $separatedByMonth
	 */
	public function setSeparatedByMonth(bool $separatedByMonth): void
	{
		$this->separatedByMonth = $separatedByMonth;
	}
	
	/**
	 * @return string
	 */
	private function addCurrentMonth()
	{
		return strtolower(date('F', time())) . '/';
	}
}