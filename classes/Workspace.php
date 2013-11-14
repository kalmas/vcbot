<?php 
class Workspace{
	/**
	 * @var string
	 */
	protected $url;
	/**
	 * @var string
	 */
	protected $directoryPath;
	/**
	 * @var MessageLog
	 */
	protected $messageLog;
	/**
	 * @var CommandClient
	 */
	protected $commandClient;
	/**
	 * @var string
	 */
	protected $repoType;
	/**
	 * @var boolean
	 */
	protected $verbose;
	/**
	 * @var string
	 */
	protected $currentBranch;
	
	/**
	 * @param string $url
	 * @param string $directoryPath
	 * @param MessageLog $messageLog
	 * @param CommandClient $commandClient
	 * @param string $type
	 * @param boolean $verbose
	 */
	public function __construct($url, $directoryPath, $messageLog, $commandClient, $type, $verbose){
		$this->url = $url;
		$this->directoryPath = $directoryPath;
		$this->messageLog = $messageLog;
		$this->commandClient = $commandClient;		
		$this->repoType = $type;
	}
	
	/**
	 * This is just a shunt for testing.
	 * If you're calling this outside of a test, you're doing it wrong.
	 * @param string $currentBranch
	 * @returns void
	 */
	public function setCurrentBranch($currentBranch){
		$this->currentBranch = $currentBranch;
	}

}
