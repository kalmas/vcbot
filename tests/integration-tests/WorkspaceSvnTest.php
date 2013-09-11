<?php 

class WorkspaceSvnTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var WorkspaceSvn
	 */
	private $ws;
	
	/**
	 * @var CommandClient
	 */
	private $client;
	
	public function setUp(){
		parent::setUp();
		
		$log = new MessageLog();
		$this->client = new CommandClient($log);
		$this->ws = new WorkspaceSvn('http://svn.frlabs.com/svn/frc', '~/frc_push', $log, $this->client);
		
		$ws = $this->ws;
		
		$ws->createTicket('vcbot_testTicket');
		$ws->createRelease('vcbot_testRelease');
		
		$ws->createTicket('vcbot_testTicket_merge');
		
	}
	
	public function tearDown(){
		parent::tearDown();
		
		$ws = $this->ws;
		
		$ws->deleteTicket('vcbot_testTicket');
		$ws->deleteRelease('vcbot_testRelease');

		$ws->deleteTicket('vcbot_testTicket_merge');
	}
	
	public function test_ticketExists(){
		$ws = $this->ws;
		
		$this->assertTrue($ws->ticketExists('ticket_1234_example'));
		$this->assertFalse($ws->ticketExists('not_a_real_ticket'));
	}
	
	public function test_releaseExists(){
		$ws = $this->ws;
	
		$this->assertTrue($ws->releaseExists('A0111'));
		$this->assertFalse($ws->releaseExists('not_a_real_release'));
	}
	
	public function test_createTicket_deleteTicket(){
		$ws = $this->ws;
		
		$this->assertFalse($ws->ticketExists('vcbot_test_createTicket_deleteTicket'));
		$this->assertTrue($ws->createTicket('vcbot_test_createTicket_deleteTicket'));
		$this->assertTrue($ws->ticketExists('vcbot_test_createTicket_deleteTicket'));
		$this->assertTrue($ws->deleteTicket('vcbot_test_createTicket_deleteTicket'));
		$this->assertFalse($ws->ticketExists('vcbot_test_createTicket_deleteTicket'));		
	}
	
	public function test_createRelease_deleteRelease(){
		$ws = $this->ws;
	
		$this->assertFalse($ws->releaseExists('vcbot_test_createRelease_deleteRelease'));
		$this->assertTrue($ws->createRelease('vcbot_test_createRelease_deleteRelease'));
		$this->assertTrue($ws->releaseExists('vcbot_test_createRelease_deleteRelease'));
		$this->assertTrue($ws->deleteRelease('vcbot_test_createRelease_deleteRelease'));
		$this->assertFalse($ws->releaseExists('vcbot_test_createRelease_deleteRelease'));
	}
	
	public function test_switchToTicket(){
		$ws = $this->ws;
		
		$this->assertTrue($ws->switchToTicket('vcbot_testTicket'));
		
		$this->assertEquals('branches/vcbot_testTicket', $ws->getCurrentBranch());
		
		$infoXml = $ws->getWorkspaceInfo();
		$info = new SimpleXMLElement($infoXml);
		$url = (string) $info->entry->url;
		$branch = str_replace('http://svn.frlabs.com/svn/frc' . '/', '', $url);
		$this->assertEquals('branches/vcbot_testTicket', $branch);		
	}
	
	public function test_switchToRelease(){
		$ws = $this->ws;
	
		$this->assertTrue($ws->switchToRelease('vcbot_testRelease'));
	
		$this->assertEquals('releases/vcbot_testRelease', $ws->getCurrentBranch());
	
		$infoXml = $ws->getWorkspaceInfo();
		$info = new SimpleXMLElement($infoXml);
		$url = (string) $info->entry->url;
		$branch = str_replace('http://svn.frlabs.com/svn/frc' . '/', '', $url);
		$this->assertEquals('releases/vcbot_testRelease', $branch);
	}
	
	public function test_mergeTicket_no_conflict(){
		$ws = $this->ws;
		
		// Go to first ticket
		$ws->switchToTicket('vcbot_testTicket_merge');
		
		// Make sure the test file isnt there already somehow
		$this->assertFalse($this->client->execute('ls ~/frc_push/vcbottest'));
		
		// Make a test file and commit it
		$this->client->execute('touch ~/frc_push/vcbottest');
		$this->client->execute('svn add ~/frc_push/vcbottest');
		$this->client->execute('svn commit -m "automated testing: made a new file" ~/frc_push');
		
		// Go to second ticket
		$ws->switchToTicket('vcbot_testTicket');
		
		// Assert the test file isn't already here either
		$this->assertFalse($this->client->execute('ls ~/frc_push/vcbottest'));
		
		// Merge first ticket into second
		$this->assertTrue($ws->mergeTicket('vcbot_testTicket_merge'));
		
		// Make sure test file is here now
		$this->assertTrue($this->client->execute('ls ~/frc_push/vcbottest'));
	}
	
	public function test_mergeTicket_tree_conflict(){
		$ws = $this->ws;
		
		// Go to first ticket
		$ws->switchToTicket('vcbot_testTicket_merge');
		
		// Make sure the test file isnt there already somehow
		$this->assertFalse($this->client->execute('ls ~/frc_push/vcbottest'));
		
		// Make a test file and commit it
		$this->client->execute('touch ~/frc_push/vcbottest');
		$this->client->execute('svn add ~/frc_push/vcbottest');
		$this->client->execute('svn commit -m "automated testing: made a new file" ~/frc_push');
		
		// Go to second ticket
		$ws->switchToTicket('vcbot_testTicket');
		
		// Assert the test file isn't already here either
		$this->assertFalse($this->client->execute('ls ~/frc_push/vcbottest'));
		
		// Make a test file agian and commit it
		$this->client->execute('touch ~/frc_push/vcbottest');
		$this->client->execute('svn add ~/frc_push/vcbottest');
		$this->client->execute('svn commit -m "automated testing: made a new file agian" ~/frc_push');
		
		// Obligatory update
		$this->client->execute('svn up ~/frc_push');
		
		// Merge first ticket into second, should fail on conflict
		$this->assertFalse($ws->mergeTicket('vcbot_testTicket_merge'));
	}
	
	public function test_mergeTicket_text_conflict(){
		$ws = $this->ws;
		
		// Go to first ticket
		$ws->switchToTicket('vcbot_testTicket_merge');
		
		// Make a test file and commit it
		$this->client->execute('touch ~/frc_push/vcbottest');
		$this->client->execute('svn add ~/frc_push/vcbottest');
		$this->client->execute('svn commit -m "automated testing: made a new file" ~/frc_push');
		
		// Go to second ticket
		$ws->switchToTicket('vcbot_testTicket');
		
		// Merge first ticket into second
		$this->assertTrue($ws->mergeTicket('vcbot_testTicket_merge'));
		
		// Now, change the file and commit that
		$this->client->execute('echo "the file should say this" > ~/frc_push/vcbottest');
		$this->client->execute('svn commit -m "automated testing: modified file" ~/frc_push');
		
		// Switch back to first ticket
		$ws->switchToTicket('vcbot_testTicket_merge');
		
		// Change the file agian here and commit (tired yet?)
		$this->client->execute('echo "no, the file shouldnt say that" > ~/frc_push/vcbottest');
		$this->client->execute('svn commit -m "automated testing: modified file once more" ~/frc_push');
		
		// Obligatory update
		$this->client->execute('svn up ~/frc_push');
		
		// Merge second ticket into the first, should fail on conflict
		$this->assertFalse($ws->mergeTicket('vcbot_testTicket'));
	}
	
	
}