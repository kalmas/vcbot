<?php
 
class Bot{
	
	private $workspaces = array();
	
	public function __construct($config = null){
		$configObj = json_decode($config);
		
		$messageLog = new MessageLog();
		$commandClient = new CommandClient();		
		
		foreach($configObj->workspaces as $wsConfig){
			$workspace = new WorkspaceSvn($wsConfig->url, $wsConfig->directoryPath, $messageLog, $commandClient);
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
			throw new Exception('Workspace not registered');
		}
		
		return $this->workspaces[$workspaceName];
	}
	
	/**
	 * @return array
	 */
	public function getWorkspaceNames(){
		return array_keys($this->workspaces);
	}
	
	
}
