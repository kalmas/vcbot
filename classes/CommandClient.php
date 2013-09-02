<?php 

class CommandClient{

	/**
	 * @var MessageLog
	 */
	private $messageLog;
	/**
	 * @var array
	 */
	private $lastResponse;
	/**
	 * @var int
	 */
	private $lastReturnValue;
	
	/**
	 * @param MessageLog $messageLog
	 */
	public function __construct($messageLog = null){
		$this->messageLog = $messageLog;
	}
	
	/**
	 * @param MessageLog $messageLog
	 */
	public function setMessageLog($messageLog){
		$this->messageLog = $messageLog;
	}
	
	/**
	 * @param string $command
	 * @return boolean
	 */
	public function execute($command){
		$this->messageLog->write($command, MessageLog::COMMAND);
		
		$returnVal = 0;
		exec($command, $response, $returnVal);
		$this->lastResponse = $response;
		$this->lastReturnValue = $returnVal;
		
		$this->messageLog->write($response, MessageLog::RESPONSE);

		// If $returnVal == 0, it means no errors occured
		if($returnVal === 0){
			return true;
		} else {
			return false;
		}		
	}
	
	/**
	 * @return array
	 */
	public function getLastResponse(){
		return $this->lastResponse;
	}
	
	/**
	 * @return int
	 */
	public function getLastReturnValue(){
		return $this->lastReturnValue;
	}
	
}
