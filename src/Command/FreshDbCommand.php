<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FreshDbCommand extends Command
{
	protected static $defaultName = 'app:doctrine:fresh';
	
	protected function configure()
	{
		$this
			->setDescription('Drop and recreate all table schemas. Run fixtures.');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->setHidden(true);
		
		$app = $this->getApplication();
		
		$dropDbCmd      = $app->find('doctrine:database:drop');
		$dropDbCmdInput = new ArrayInput(['command' => 'doctrine:database:drop', '--force' => true]);
		
		$createDbCmd      = $app->find('doctrine:database:create');
		$createDbCmdInput = new ArrayInput(['command' => 'doctrine:database:create']);
		
		$createSchemaCmd      = $app->find('doctrine:schema:update');
		$createSchemaCmdInput = new ArrayInput(['command' => 'doctrine:schema:update', '--force' => true]);
		
		$runFixturesCmd      = $app->find('doctrine:fixtures:load');
		$runFixturesCmdInput = new ArrayInput(['command' => 'doctrine:fixtures:load']);
		$runFixturesCmdInput->setInteractive(false);
		
		$migrateSession      = $app->find('migrate:session:db');
		$migrateSessionInput = new ArrayInput(['command' => 'migrate:session:db']);
		
		$migrateRemember      = $app->find('migrate:remember:db');
		$migrateRememberInput = new ArrayInput(['command' => 'migrate:remember:db']);
		
		$dropDbCmd->run($dropDbCmdInput, $output);
		
		$createDbCmd->run($createDbCmdInput, $output);
		
		$createSchemaCmd->run($createSchemaCmdInput, $output);
		
		$runFixturesCmd->run($runFixturesCmdInput, $output);
		
		$migrateSession->run($migrateSessionInput, $output);
		
		$migrateRemember->run($migrateRememberInput, $output);
		
		return 0;
	}
}
