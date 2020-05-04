<?php

declare(strict_types = 1);

namespace App\Command;

use App\Service\Maintenance\CounterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContentCounterCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'app:content:count';
	
	/**
	 * @var CounterService
	 */
	private $counterService;
	
	public function __construct(CounterService $counterService)
	{
		parent::__construct();
		$this->counterService = $counterService;
	}
	
	/**
	 * Configure.
	 */
	protected function configure()
	{
		$this
			->setDescription('Count and save to the db a fresh count of all posts, comments, pictures and galleries.');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->setHidden(true);
		
		$this->counterService->mapCounts();
		$this->counterService->save();
		
		return 0;
	}
}
