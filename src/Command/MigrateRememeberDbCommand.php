<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateRememeberDbCommand extends Command
{
	protected static $defaultName = 'migrate:remember:db';
	
	protected function configure()
	{
		$this
			->setDescription('Create a table for symfony remember me');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->setHidden(true);
		
		$app = $this->getApplication();
		
		$runSqlCmd      = $app->find('doctrine:query:sql');
		$runSqlCmdInput = new ArrayInput(
			[
				'command' => 'doctrine:query:sql',
				'sql'     => 'CREATE TABLE `rememberme_token` (
				`series`   char(88)     UNIQUE PRIMARY KEY NOT NULL,
				`value`    char(88)     NOT NULL,
				`lastUsed` datetime     NOT NULL,
				`class`    varchar(100) NOT NULL,
				`username` varchar(200) NOT NULL
			);',
			]
		);
		
		$runSqlCmd->run($runSqlCmdInput, $output);
		
		return 0;
	}
}
