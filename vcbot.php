<?php 
require_once 'bootstrap.php';

$config = file_get_contents(dirname(__FILE__) . '/config.json');

$bot = new Bot($config);

switch($argv[1]){
	// makeTicket frms ticket_1234
	case 'makeTicket':
	case 'maketicket':
	case 'mt':
	case 'createbranch':
	case 'cb':
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
	case 'makerelease':
	case 'mr':
	case 'createrelease':
	case 'cr':
		$wsName = strtolower($argv[2]);
		$releaseName = $argv[3];
		
		try{
			$bot->getWorkspace($wsName)->createRelease($releaseName);
		}catch(Exception $e){
			die($e->getMessage());
		}
		break;
	// deleteTicket frms ticket_1234
	case 'deleteTicket':
	case 'deleteticket':
	case 'dt':
	case 'deletebranch':
	case 'db':
		$wsName = strtolower($argv[2]);
		$ticketName = $argv[3];
	
		try{
			$bot->getWorkspace($wsName)->deleteTicket($ticketName);
		}catch(Exception $e){
			die($e->getMessage());
		}
		break;
	// deleteRelease frms Sep0813
	case 'deleteRelease':
	case 'deleterelease':
	case 'dr':
		$wsName = strtolower($argv[2]);
		$releaseName = $argv[3];
	
		try{
			$bot->getWorkspace($wsName)->deleteRelease($releaseName);
		}catch(Exception $e){
			die($e->getMessage());
		}
		break;
	// mergeUp ticket_1234 Sep0813
	case 'mergeUp':
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


