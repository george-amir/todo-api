<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Parser;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('todo-app:install')
        ->setDescription('Execute required commands for the app.')
        ->setHelp('This command execute required commands for the app to work properly.');
    }

	private static function generateRandomString($length){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating client_id and client_secret...');
		$client_id = $this->generateRandomString(52);
		$client_secret = $this->generateRandomString(40);
		$secret = $this->generateRandomString(40);
		$output->writeln('client_id: '.$client_id);
		$output->writeln('client_secret: '.$client_secret);
		$output->writeln('app_secret: '.$secret);
		
		$yaml = new Parser();

		$yamlValue = $yaml->parse(file_get_contents('./app/config/parameters.yml'));
		$yamlValue['parameters']['client_id'] = '1_'.$client_id;
		$yamlValue['parameters']['client_secret'] = $client_secret;
		$yamlValue['parameters']['secret'] = $secret;
		
		$em = $this->getContainer()->get('doctrine')->getManager();

        $RAW_QUERY = "INSERT INTO `oauth2_clients` VALUES (NULL, '".$client_id."', 'a:0:{}', '".$client_secret."', 'a:1:{i:0;s:8:\"password\";}');";
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
		
		$yaml = Yaml::dump($yamlValue);
		
		file_put_contents('./app/config/parameters.yml', $yaml);
    }
}