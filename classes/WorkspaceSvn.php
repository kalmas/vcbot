<?php 
class WorkspaceSvn extends Workspace implements IRepositoryRepo {	
	const TYPE = 'svn';
	private $cmdAppend;
	private $ticketBase = 'branches';
	private $releaseBase = 'releases';
	
	/**
	 * @param string $url
	 * @param string $directoryPath
	 * @param MessageLog $messageLog
	 * @param CommandClient $commandClient
	 * @param boolean $verbose
	 */
	public function __construct($url, $directoryPath, $messageLog, $commandClient, $verbose = false){
		parent::__construct($url, $directoryPath, $messageLog, $commandClient, self::TYPE, $verbose);
		if(!$this->verbose){
			$this->cmdAppend = ' 2>&1';
		}
	}
	
	/**
	 * @param string $ticketName
	 * @return string
	 */
	private function getTicketPath($ticketName){
		return "{$this->ticketBase}/{$ticketName}";
	}
	
	/**
	 * @param string $releaseName
	 * @return string
	 */
	private function getReleasePath($releaseName){
		return "{$this->releaseBase}/{$releaseName}";
	}
	
	/**
	 * Determine if provided svn path exists
	 * @param string $url
	 * @return boolean
	 */
	private function pathExists($path){
		$this->messageLog->write("Checking for existance of {$path}");
		$cmd = "svn ls {$this->url}/{$path} {$this->cmdAppend}";
		$success = $this->commandClient->execute($cmd);

		if($success){
			$this->messageLog->write("{$path} found");
			return true;
		} else {
			$this->messageLog->write("{$path} NOT found");
			return false;
		}
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::ticketExists()
	 */
	public function ticketExists($ticketName){
		$path = $this->getTicketPath($ticketName);
		return $this->pathExists($path);
	}

	/* (non-PHPdoc)
	 * @see IRepositoryRepo::releaseExists()
	 */
	public function releaseExists($releaseName) {
		$path = $this->getReleasePath($releaseName);
		return $this->pathExists($path);
	}

	/**
	 * Create branch by copying from $basePath. Return true if branch was copied.
	 * Return false if branch already exists or something else goes wrong.
	 * @param string $path
	 * @param string $basePath
	 * @return boolean
	 */
	private function copyBranch($path, $basePath = 'trunk'){
		if($this->pathExists($path)){
			$this->messageLog->write("Can't create {$path}, branch already exists");
			return false;
		}
		if(!$this->pathExists($basePath)){
			$this->messageLog->write("Cannot copy from {$basePath}, branch dosen't exists");
			return false;
		}
		$this->messageLog->write("Creating {$path} from {$basePath}");
		$cmd = "svn copy {$this->url}/{$basePath} {$this->url}/{$path}"
				. " -m 'Creation of {$path}, Copied from {$basePath}' {$this->cmdAppend}";
		return $this->commandClient->execute($cmd);
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::createTicket()
	 */
	public function createTicket($ticketName) {
		$path = $this->getTicketPath($ticketName);
		return $this->copyBranch($path);
	}

	/* (non-PHPdoc)
	 * @see IRepositoryRepo::createRelease()
	 */
	public function createRelease($releaseName) {
		$path = $this->getReleasePath($releaseName);
		return $this->copyBranch($path);
	}
	
	private function switchToBranch($path){
		$this->messageLog->write("Switching {$this->directoryPath} to {$path}");
		$cmd = "svn sw {$this->url}/{$path} {$this->directoryPath} {$this->cmdAppend}";
		return $this->commandClient->execute($cmd);
	}

	/* (non-PHPdoc)
	 * @see IRepositoryRepo::switchToTicket()
	 */
	public function switchToTicket($ticketName) {
		$path = $this->getTicketPath($ticketName);
		if($this->switchToBranch($path)){
			$this->currentBranch = $path;
			return true;
		}
		return false;	
	}

	/* (non-PHPdoc)
	 * @see IRepositoryRepo::switchToRelease()
	 */
	public function switchToRelease($releaseName) {
		$path = $this->getReleasePath($releaseName);
		if($this->switchToBranch($path)){
			$this->currentBranch = $path;
			return true;
		}
		return false;	
	}
	
	/**
	 * Determine if there are conflicts listed in the result message
	 * @param array $resultArray
	 * @return boolean
	 */
	public function detectConflict($resultArray){
		foreach($resultArray as $line){
			if(strstr($line, 'Summary of conflicts:')){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Merge branch into workspace and commit
	 * @param string $path
	 * @return boolean
	 */
	private function mergeBranch($path){
		$this->messageLog->write("Merging {$path} into {$this->directoryPath}");
		$cmd = "svn merge {$this->url}/{$path} {$this->directoryPath} {$this->cmdAppend}";
		if(!$this->commandClient->execute($cmd)){ // If merge failed, stop here
			return false;
		}
		
		if($this->detectConflict($this->commandClient->getLastResponse())){
			$this->messageLog->write('Conflict encountered, please resolve.', MessageLog::IMPORTANT);
			return false;
		} else {
			// Commit merge
			$cmd = "svn commit -m 'merged {$path} into {$this->currentBranch}' {$this->directoryPath} {$this->cmdAppend}";
			return $this->commandClient->execute($cmd);
		}
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::mergeTicket()
	 */
	public function mergeTicket($ticketName){
		$path = $this->getTicketPath($ticketName);
		return $this->mergeBranch($path);
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::mergeRelease()
	 */
	public function mergeRelease($releaseName){
		$path = $this->getReleasePath($releaseName);
		return $this->mergeBranch($path);
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::getCurrentBranch()
	 */
	public function getCurrentBranch(){
		if(is_null($this->currentBranch)){
			$infoXml = $this->getWorkspaceInfo();
			$info = new SimpleXMLElement($infoXml);
			$url = (string) $info->entry->url;
			$branch = str_replace($this->url . '/', '', $url);
			$this->currentBranch = $branch;
		}
		return $this->currentBranch;
	}
	
	private function deleteBranch($path){
		$this->messageLog->write("Deleting {$path}");
		$cmd = "svn delete {$this->url}/{$path} -m 'deleting {$path}' {$this->cmdAppend}";
		return $this->commandClient->execute($cmd);
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::deleteTicket()
	 */
	public function deleteTicket($ticketName){
		$path = $this->getTicketPath($ticketName);
		return $this->deleteBranch($path);
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::deleteRelease()
	 */
	public function deleteRelease($releaseName){
		$path = $this->getReleasePath($releaseName);
		return $this->deleteBranch($path);
	}
	
	private function moveBranch($path, $newPath){
		if($this->copyBranch($newPath, $path)){
			return $this->deleteBranch($path);
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::moveTicket()
	 */
	public function moveTicket($ticketName, $newTicketName){
		$path = $this->getTicketPath($ticketName);
		$newPath = $this->getTicketPath($newTicketName);
		return $this->moveBranch($path, $newPath);
	}
	
	/* (non-PHPdoc)
	 * @see IRepositoryRepo::moveRelease()
	 */
	public function moveRelease($releaseName, $newReleaseName){
		$path = $this->getReleasePath($releaseName);
		$newPath = $this->getReleasePath($newReleaseName);
		return $this->moveBranch($path, $newPath);
	}
	
	/**
	 * Return svn info xml
	 * @return string
	 */
	public function getWorkspaceInfo(){
		$cmd = "svn info --xml {$this->directoryPath} {$this->cmdAppend}";
		$success = $this->commandClient->execute($cmd);
		if($success){
			$response = $this->commandClient->getLastResponse();
			$string = '';
			foreach($response as $line){
				$string .= $line . "\n";
			}
			
			return $string;
		}
		return '';
	}

}
