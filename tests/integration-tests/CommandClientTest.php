<?php 

class CommandClientTest extends PHPUnit_Framework_TestCase{
	
	public function test_execute(){
		$messageLog = $this->getMock('MessageLog', array('write'));
		$messageLog->expects($this->at(0))
			->method('write')
			->with('ls -l', MessageLog::COMMAND);
		$messageLog->expects($this->at(1))
			->method('write');
		$client = new CommandClient($messageLog);
		
		$success = $client->execute('ls -l');
		$this->assertTrue($success);
		
	}

}