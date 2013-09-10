<?php 

class MessageLogTest extends PHPUnit_Framework_TestCase{
	
	public function test_write_writes_string(){
		$messageLog = new MessageLog();
		
		ob_start();
		$messageLog->write('hello');
		$output = ob_get_clean();
		
		$this->assertEquals("hello\n\n", $output);		
	}
	
	public function test_write_writes_array(){
		$messageLog = new MessageLog();
	
		ob_start();
		$messageLog->write(array('hello', 'world'));
		$output = ob_get_clean();
	
		$this->assertEquals("hello\nworld\n\n", $output);
	}
}
