<?php 

interface IRepositoryRepo {
	/**
	 * @param string
	 * @return boolean
	 */
	public function ticketExists($ticketName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function releaseExists($releaseName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function createTicket($ticketName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function createRelease($releaseName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function moveTicket($ticketName, $newTicketName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function moveRelease($releaseName, $newReleaseName);	
	/**
	 * @param string
	 * @return boolean
	 */
	public function deleteTicket($ticketName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function deleteRelease($releaseName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function switchToTicket($ticketName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function switchToRelease($releaseName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function mergeTicket($ticketName);
	/**
	 * @param string
	 * @return boolean
	 */
	public function mergeRelease($releaseName);
	/**
	 * @return string 
	 */
	public function getCurrentBranch();

}
