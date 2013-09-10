<?php 

class MessageLogTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * No assertions here, just a quick way to inspect output visually
	 */
	public function test_write(){
		$messageLog = new MessageLog();
		
		$messageLog->write('About to test');
		
		$messageLog->write('This is a command', MessageLog::COMMAND);
		$messageLog->write(array('x', 'This is a commands response', 'cool'), MessageLog::RESPONSE);
		
		$messageLog->write('This is important', MessageLog::IMPORTANT);
		
		$messageLog->write('Done testing');
		
	}

}