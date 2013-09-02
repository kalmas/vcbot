<?php 

class WorkspaceSvnTest extends PHPUnit_Framework_TestCase{
	
	public function test_ticketExists_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
		// One 'svn ls' call shoule get issued
		$client->expects($this->once())
			->method('execute')
			->with('svn ls http://svn.example.com/vcbot/branches/ticket_1234  2>&1');
		
		$log = $this->getMock('MessageLog', array('write'));
		
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
		
		$ws->ticketExists('ticket_1234');	
	}
	
	public function test_ticketExists_returns_true_when_ticket_exists(){
		$client = $this->getMock('CommandClient', array('execute'));
		// Return true, ticket found
		$client->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$result = $ws->ticketExists('ticket_1234');
		$this->assertTrue($result);
	}
	
	public function test_ticketExists_returns_false_when_ticket_dosent_exist(){
		$client = $this->getMock('CommandClient', array('execute'));
		// Return false, ticket not found
		$client->expects($this->once())
			->method('execute')
			->will($this->returnValue(false));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$result = $ws->ticketExists('ticket_1234');
		$this->assertFalse($result);
	}
	
	public function test_ticketExists_logs_correctly(){
		$client = $this->getMock('CommandClient', array('execute'));
		
		$log = $this->getMock('MessageLog', array('write'));
		
		$log->expects($this->exactly(2))
			->method('write');
		
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
		
		$ws->ticketExists('ticket_1234');
	}
	
	public function test_releaseExists_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
		// One 'svn ls' call shoule get issued
		$client->expects($this->once())
			->method('execute')
			->with('svn ls http://svn.example.com/vcbot/releases/Aug3113  2>&1');
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$ws->releaseExists('Aug3113');
	}
	
	public function test_createTicket_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
		
		// Ticket exists? No.
		$client->expects($this->at(0))
			->method('execute')
			->will($this->returnValue(false));
		// Base exists? Yes.
		$client->expects($this->at(1))
			->method('execute')
			->will($this->returnValue(true));
		// Copy base to new ticket
		$client->expects($this->at(2))
			->method('execute')
			->with("svn copy http://svn.example.com/vcbot/trunk http://svn.example.com/vcbot/branches/ticket_1234 -m 'Creation of branches/ticket_1234, Copied from trunk'  2>&1");
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$ws->createTicket('ticket_1234');
	}
	
	public function test_createTicket_stops_and_returns_false_if_ticket_already_exists(){
		$client = $this->getMock('CommandClient', array('execute'));
				
		// Ticket exists? Yes.
		$client->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));

		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$result = $ws->createTicket('ticket_1234');
		
		$this->assertFalse($result);
	}
	
	public function test_createTicket_stops_and_returns_false_if_base_dosent_exist(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// Ticket exists? No.
		$client->expects($this->at(0))
			->method('execute')
			->will($this->returnValue(false));
		// Base exists? No.
		$client->expects($this->at(1))
			->method('execute')
			->will($this->returnValue(false));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$result = $ws->createTicket('ticket_1234');
	
		$this->assertFalse($result);
	}
	
	public function test_createRelease_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// Ticket exists? No.
		$client->expects($this->at(0))
			->method('execute')
			->will($this->returnValue(false));
		// Base exists? Yes.
		$client->expects($this->at(1))
			->method('execute')
			->will($this->returnValue(true));
		// Copy base to new ticket
		$client->expects($this->at(2))
			->method('execute')
			->with("svn copy http://svn.example.com/vcbot/trunk http://svn.example.com/vcbot/releases/Sep0113 -m 'Creation of releases/Sep0113, Copied from trunk'  2>&1");
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$ws->createRelease('Sep0113');
	}
	
	public function test_switchToTicket_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));

		// Switch workspace
		$client->expects($this->at(0))
			->method('execute')
			->with("svn sw http://svn.example.com/vcbot/branches/ticket_1234 ~/test  2>&1");
		
		$log = $this->getMock('MessageLog', array('write'));
		
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
		
		$ws->switchToTicket('ticket_1234');		
	}
	
	public function test_switchToTicket_returns_false_on_failure(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// Switch workspace... but something went wrong
		$client->expects($this->at(0))
			->method('execute')
			->will($this->returnValue(false));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$result = $ws->switchToTicket('ticket_1234');
		
		$this->assertFalse($result);
	}
	
	public function test_switchToTicket_changes_workspace_branch(){
		$client = $this->getMock('CommandClient', array('execute'));
		
		// Switch workspace
		$client->expects($this->at(0))
			->method('execute')
			->will($this->returnValue(true));
		
		$log = $this->getMock('MessageLog', array('write'));
		
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
		
		$ws->switchToTicket('ticket_1234');
		
		$this->assertEquals('branches/ticket_1234', $ws->getCurrentBranch());		
	}
	
	public function test_switchToRelease_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// Switch workspace
		$client->expects($this->at(0))
			->method('execute')
			->with("svn sw http://svn.example.com/vcbot/releases/Sep0113 ~/test  2>&1");
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$ws->switchToRelease('Sep0113');
	}
	
	public function test_getCurrentBranch(){
		$client = $this->getMock('CommandClient', array('execute', 'getLastResponse'));
		
		// Get info
		$client->expects($this->at(0))
			->method('execute')
			->with("svn info --xml ~/test  2>&1")
			->will($this->returnValue(true));
		$infoStr = <<<EOD
<?xml version="1.0"?>
<info>
	<entry
	   kind="dir"
	   path="."
	   revision="1">
		<url>http://svn.example.com/vcbot/branches/ticket_1234</url>
		<repository>
			<root>http://svn.example.com/vcbot</root>
			<uuid>5e7d134a-54fb-0310-bd04-b611643e5c25</uuid>
		</repository>
		<wc-info>
			<schedule>normal</schedule>
			<depth>infinity</depth>
		</wc-info>
		<commit
	   		revision="1">
			<author>sally</author>
			<date>2003-01-15T23:35:12.847647Z</date>
		</commit>
	</entry>
</info>
EOD;
		// Return Xml
		$client->expects($this->at(1))
			->method('getLastResponse')
			->will($this->returnValue($infoStr));
		
		$log = $this->getMock('MessageLog', array('write'));
		
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
		
		$currentBranch = $ws->getCurrentBranch();
		
		$this->assertEquals('branches/ticket_1234', $currentBranch);
	}
	
	public function test_mergeTicket_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// Merge
		$client->expects($this->at(0))
			->method('execute')
			->with("svn merge http://svn.example.com/vcbot/branches/ticket_1234 ~/test  2>&1")
			->will($this->returnValue(true));
		$client->expects($this->at(1))
			->method('execute')
			->with("svn commit -m 'merged branches/ticket_1234 into releases/Sep0113'")
			->will($this->returnValue(true));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
		$ws->setCurrentBranch('releases/Sep0113');
	
		$ws->mergeTicket('ticket_1234');
	}
	
	public function test_deleteTicket_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
		
		// Delete
		$client->expects($this->at(0))
			->method('execute')
			->with("svn delete http://svn.example.com/vcbot/branches/ticket_1234 -m 'deleting branches/ticket_1234'  2>&1")
			->will($this->returnValue(true));
		
		$log = $this->getMock('MessageLog', array('write'));
		
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
		
		$ws->deleteTicket('ticket_1234');
	}
	
	public function test_deleteRelease_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// Delete
		$client->expects($this->at(0))
			->method('execute')
			->with("svn delete http://svn.example.com/vcbot/releases/Sep0113 -m 'deleting releases/Sep0113'  2>&1")
			->will($this->returnValue(true));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$ws->deleteRelease('Sep0113');
	}
	
	public function test_moveTicket_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// New Branch exists? No.
		$client->expects($this->at(0))
			->method('execute')
			->will($this->returnValue(false));
		// Base Branch exists? Yes.
		$client->expects($this->at(1))
			->method('execute')
			->will($this->returnValue(true));
		// Copy
		$client->expects($this->at(2))
			->method('execute')
			->with("svn copy http://svn.example.com/vcbot/branches/ticket_1234 http://svn.example.com/vcbot/branches/ticket_1234A -m 'Creation of branches/ticket_1234A, Copied from branches/ticket_1234'  2>&1")
			->will($this->returnValue(true));
		// Delete
		$client->expects($this->at(3))
			->method('execute')
			->with("svn delete http://svn.example.com/vcbot/branches/ticket_1234 -m 'deleting branches/ticket_1234'  2>&1")
			->will($this->returnValue(true));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$ws->moveTicket('ticket_1234', 'ticket_1234A');
	}
	
	public function test_moveRelease_issues_correct_command(){
		$client = $this->getMock('CommandClient', array('execute'));
	
		// New Branch exists? No.
		$client->expects($this->at(0))
			->method('execute')
			->will($this->returnValue(false));
		// Base Branch exists? Yes.
		$client->expects($this->at(1))
			->method('execute')
			->will($this->returnValue(true));
		// Copy
		$client->expects($this->at(2))
			->method('execute')
			->with("svn copy http://svn.example.com/vcbot/releases/Sep0113 http://svn.example.com/vcbot/releases/Sep0113A -m 'Creation of releases/Sep0113A, Copied from releases/Sep0113'  2>&1")
			->will($this->returnValue(true));
		// Delete
		$client->expects($this->at(3))
			->method('execute')
			->with("svn delete http://svn.example.com/vcbot/releases/Sep0113 -m 'deleting releases/Sep0113'  2>&1")
			->will($this->returnValue(true));
	
		$log = $this->getMock('MessageLog', array('write'));
	
		$ws = new WorkspaceSvn('http://svn.example.com/vcbot', '~/test', $log, $client);
	
		$ws->moveRelease('Sep0113', 'Sep0113A');
	}
	
}
