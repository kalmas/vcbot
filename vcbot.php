<?php 
require_once 'bootstrap.php';

$config = file_get_contents('./config.json');

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
		$ticket = $argv[2];
		$release = $argv[3];
		$workspaces = array();
		if(isset($argv[4])){
			$workspaces[] = strtolower($argv[4]);
		}else{
			$workspaces = $bot->getWorkspaceNames();
		}

		// merge each
	
}


