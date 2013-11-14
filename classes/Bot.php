<?php
 
class Bot{
	
	/**
	 * @var MessageLog
	 */
	private $messageLog = null;
	private $workspaces = array();
	
	/**
	 * @param string $config
	 */
	public function __construct($config = null){
		$configObj = json_decode($config);
		
		$this->messageLog = new MessageLog();
		$commandClient = new CommandClient($this->messageLog);		
		
		foreach($configObj->workspaces as $wsConfig){
			$workspace = new WorkspaceSvn($wsConfig->url
					, $wsConfig->directoryPath
					, $this->messageLog
					, $commandClient
					, false
					, $wsConfig->ticketBase
					, $wsConfig->releaseBase
				);
			$this->registerWorkspace(strtolower($wsConfig->name), $workspace);
		}
	}
	
	/**
	 * @param string $name
	 * @param IRepositoryRepo $workspace
	 */
	public function registerWorkspace($name, IRepositoryRepo $workspace){
		$this->workspaces[$name] = $workspace;
	}
	
	/**
	 * @param string $workspaceName
	 * @return boolean 
	 */
	public function hasWorkspace($workspaceName){
		return array_key_exists($workspaceName, $this->workspaces);
	}
	
	/**
	 * @param string $workspaceName
	 * @throws Exception
	 * @return IRepositoryRepo
	 */
	public function getWorkspace($workspaceName){
		if(!$this->hasWorkspace($workspaceName)){
			throw new Exception("Workspace {$workspaceName} not registered");
		}
		
		return $this->workspaces[$workspaceName];
	}
	
	/**
	 * Attempt to merge $ticket into $release for all registered workspaces,
	 * or for one specific workspace if specified by 3rd param
	 * 
	 * If ticket dosen't exist in workspace repo, no merge attempt will be made
	 * 
	 * If release dosne't exist in workspace repo, this will create it
	 * 
	 * @param string $ticket
	 * @param string $release
	 * @param string $workspaceName
	 * @throws Exception
	 * @return void
	 */
	public function mergeUp($ticket, $release, $workspaceName = null){
		// Decide if we are working on one specific workspace, or all of them
		if(!is_null($workspaceName)){
			$workspaceNames = array();
			$workspaceNames[] = $workspaceName;
		} else {
			$workspaceNames = array_keys($this->workspaces);
		}
		
		foreach($workspaceNames as $name){
			$ws = $this->getWorkspace($name);
			// Does ticket exist in this workspace's repo? If not, move on to next.
			if(!$ws->ticketExists($ticket)){
				continue;
			}
			// Create a release if it dosen't exist
			$ws->createRelease($release);
			// Switch workspace to release
			if(!$ws->switchToRelease($release)){
				throw new Exception("Couldn't switch {$name} workspace to release {$release}.");
			}
			// Preform merge
			$ws->mergeTicket($ticket);
		}
	}
	
	/**
	 * Creates a new release branch from trunk, remerges all ticket branches
	 * @param string $releaseName
	 * @param string $workspaceName
	 * @throws Exception
	 * @return void
	 */
	public function rebaseRelease($releaseName, $workspaceName) {
		$ws = $this->getWorkspace($workspaceName);
		
		// Make a list of tickets to merge
		if(!$ws->switchToRelease($releaseName)) {
			throw new Exception("Couldn't switch {$workspaceName} workspace to release {$releaseName}.");
		}
		$mergedTickets = $ws->getMergedTickets();
		if(empty($mergedTickets)) {
			throw new Exception("Couldn't find any tickets merged into {$releaseName}");
		}
		
		// Backup old release branch
		$ws->moveRelease($releaseName, $releaseName . '_old' . time());
		
		// Switch to new release branch
		$ws->createRelease($releaseName);
		if(!$ws->switchToRelease($releaseName)) {
			throw new Exception("Couldn't switch {$workspaceName} workspace to release {$releaseName}.");
		}
		
		foreach($mergedTickets as $ticket) {
			if($this->messageLog->prompt("Merge {$ticket} into new {$releaseName}?")) {
				$this->mergeUp($ticket, $releaseName, $workspaceName);
			} else {
				$this->messageLog("Skipping {$ticket}.");
			}
		}	
	}
	
	
}
