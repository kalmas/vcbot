<?php 
require_once 'bootstrap.php';

$config = file_get_contents(dirname(__FILE__) . '/config.json');

$bot = new Bot($config);

switch($argv[1]){
	// makeTicket frms ticket_1234
	case 'makeTicket':
	case 'mt':
	case 'createbranch':
		$wsName = strtolower($argv[2]);
		$ticketName = $argv[3];
		
		try{
			$bot->getWorkspace($wsName)->createTicket($ticketName);
		}catch(Exception $e){
			die($e->getMessage());
		}
		break;
	// makeRelease frms Sep0813
	case 'makeRelease':
	case 'mr':
	case 'createrelease':
		$wsName = strtolower($argv[2]);
		$releaseName = $argv[3];
		
		try{
			$bot->getWorkspace($wsName)->createRelease($releaseName);
		}catch(Exception $e){
			die($e->getMessage());
		}
		break;
	// mergeup ticket_1234 Sep0813
	case 'mergeup':
	case 'mup':
		$ticket = $argv[2];
		$release = $argv[3];
		$workspace = null;
		if(isset($argv[4])){
			$workspace = strtolower($argv[4]);
		}
		
		try{
			$bot->mergeUp($ticket, $release, $workspace);
		}catch(Exception $e){
			die($e->getMessage());
		}
		break;
}


