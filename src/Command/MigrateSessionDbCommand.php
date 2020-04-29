<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateSessionDbCommand extends Command
{
	protected static $defaultName = 'migrate:session:db';
	
	protected function configure()
	{
		$this
			->setDescription('Create a table for symfony sessions');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->setHidden(true);
		
		$app = $this->getApplication();
		
		$runSqlCmd      = $app->find('doctrine:query:sql');
		$runSqlCmdInput = new ArrayInput(
			[
				'command' => 'doctrine:query:sql',
				'sql'     => 'CREATE TABLE `sessions` (
				`sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
				`sess_data` MEDIUMBLOB NOT NULL,
				`sess_time` INTEGER UNSIGNED NOT NULL,
				`sess_lifetime` INTEGER UNSIGNED NOT NULL
				) COLLATE utf8mb4_bin, ENGINE = InnoDB;',
			]
		);
		
		$runSqlCmd->run($runSqlCmdInput, $output);
		
		return 0;
	}
}
