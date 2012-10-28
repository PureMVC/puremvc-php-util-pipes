<?php 
/**
 * PureMVC PHP MultiCore Pipes Utility Unit Tests 
 * 
 * A PHP port of Cliff Hall
 * PureMVC AS3/MultiCore Pipes Utility Unit Tests
 * 
 * Created on Jully 29, 2009
 * 
 * @version 1.0
 * @author Michel Chouinard <michel.chouinard@gmail.com>
 * @copyright PureMVC - Copyright(c) 2006-2008 Futurescale, Inc., Some rights reserved.
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
/**
 * 
 */

require_once 'PHPUnit/Framework/TestCase.php';
	
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeFitting.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/Message.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/TeeMerge.php';

 /**
 * Test the TeeMerge class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class TeeMergeTest extends PHPUnit_Framework_TestCase 
{
  	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
	}
	
	  	
  	/**
  	 * Test connecting an output and several input pipes to a merging tee. 
  	 */
  	public function testConnectingIOPipes()
  	{
  		// create input pipe
   		$output1 = new Pipe();

  		// create input pipes 1, 2, 3 and 4
   		$pipe1 = new Pipe();
   		$pipe2 = new Pipe();
   		$pipe3 = new Pipe();
   		$pipe4 = new Pipe();

  		// create splitting tee (args are first two input fittings of tee)
   		$teeMerge = new TeeMerge( $pipe1, $pipe2 );
   		
   		// connect 2 extra inputs for a total of 4
   		$connectedExtra1 = $teeMerge->connectInput( $pipe3 );
   		$connectedExtra2 = $teeMerge->connectInput( $pipe4 );

		// connect the single output
		$connected = $output1->connect($teeMerge);
		
   		// test assertions
   		$this->assertTrue( $output1 instanceof Pipe, "Expecting \$output1 instanceof Pipe" );
   		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting \$pipe1 instanceof Pipe" );
   		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe" );
   		$this->assertTrue( $pipe3 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe");
   		$this->assertTrue( $pipe4 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe" );
   		$this->assertTrue( $teeMerge instanceof TeeMerge, "Expecting \$teeMerge instanceof TeeMerge" );
   		$this->assertTrue( $connectedExtra1, "Expecting connected extra input 1" );
   		$this->assertTrue( $connectedExtra2, "Expecting connected extra input 2" );
   	}

  	/**
  	 * Test receiving messages from two pipes using a TeeMerge.
  	 */
  	public function testReceiveMessagesFromTwoPipesViaTeeMerge() 
  	{
		// create a message to send on pipe 1
  		$pipe1Message = new Message( Message::NORMAL, 
   					  				 (object)array( 'testProp' => 1 ),
   					  				 simplexml_load_string('<testMessage testAtt="Pipe 1 Message" />'),
   					  				 Message::PRIORITY_LOW);
   		
		// create a message to send on pipe 2
  		$pipe2Message = new Message( Message::NORMAL, 
   					  				 (object)array( 'testProp' => 2 ),
   					  				 simplexml_load_string('<testMessage testAtt="Pipe 2 Message" />'),
   					  				 Message::PRIORITY_HIGH);
   					  				 
  		// create pipes 1 and 2
   		$pipe1 = new Pipe();
   		$pipe2 = new Pipe();
   		
  		// create merging tee (args are first two input fittings of tee)
   		$teeMerge = new TeeMerge( $pipe1, $pipe2 );

		// create listener
   		$listener = new PipeListener( $this, 'callBackMethod' );
   		
   		// connect the listener to the tee and write the messages
   		$connected = $teeMerge->connect($listener);
   		
   		// write messages to their respective pipes
   		$pipe1written = $pipe1->write( $pipe1Message );
   		$pipe2written = $pipe2->write( $pipe2Message );
   		
   		// test assertions
		$this->assertTrue( $pipe1Message instanceof IPipeMessage, "Expecting pipe1Message instanceof IPipeMessage" );
		$this->assertTrue( $pipe2Message instanceof IPipeMessage, "Expecting pipe2Message instanceof IPipeMessage" );
		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting pipe1 instanceof Pipe" );
		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting pipe2 instanceof Pipe" );
		$this->assertTrue( $teeMerge instanceof TeeMerge, "Expecting teeMerge instanceof TeeMerge" );
		$this->assertTrue( $listener instanceof PipeListener, "Expecting listener instanceof PipeListener" );
   		$this->assertTrue( $connected, "Expecting connected listener to merging tee" );
   		$this->assertTrue( $pipe1written, "Expecting wrote message to pipe 1" );
   		$this->assertTrue( $pipe2written, "Expecting wrote message to pipe 2" );
   		
   		// test that both messages were received, then test
   		// FIFO order by inspecting the messages themselves
   		$this->assertTrue( count($this->messagesReceived) == 2, "Expecting received 2 messages" );
   		
   		// test message 1 assertions 
   		$message1 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message1 instanceof IPipeMessage, "Expecting message1 instanceof IPipeMessage" );
   		$this->assertTrue( $message1 === $pipe1Message, "Expecting message1 === pipe1Message" ); // object equality
   		$this->assertTrue( $message1->getType() == Message::NORMAL, "Expecting message1->getType() == Message::NORMAL" );
   		$this->assertTrue( $message1->getHeader()->testProp == 1, "Expecting message1->getHeader()->testProp == 1" );
   		$this->assertTrue( $message1->getBody()->attributes()->testAtt == 'Pipe 1 Message', "Expecting message1->getBody()->attributes()->testAtt == 'Pipe 1 Message'" );
   		$this->assertTrue( $message1->getPriority() == Message::PRIORITY_LOW, "Expecting message1->getPriority() == Message::PRIORITY_LOW");

   		// test message 2 assertions
   		$message2 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message2 instanceof IPipeMessage, "Expecting message2 instanceof IPipeMessage" );
   		$this->assertTrue( $message2 === $pipe2Message, "Expecting message2 === pipe2Message" ); // object equality
   		$this->assertTrue( $message2->getType() == Message::NORMAL, "Expecting message2->getType() == Message::NORMAL" );
   		$this->assertTrue( $message2->getHeader()->testProp == 2, "Expecting message2->getHeader()->testProp == 2" );
   		$this->assertTrue( $message2->getBody()->attributes()->testAtt == 'Pipe 2 Message', "Expecting message1->getBody()->attributes()->testAtt == 'Pipe 2 Message'" );
   		$this->assertTrue( $message2->getPriority() == Message::PRIORITY_HIGH, "Expecting message2->getPriority() == Message::PRIORITY_HIGH");

   	}

  	/**
  	 * Test receiving messages from four pipes using a TeeMerge.
  	 */
  	public function testReceiveMessagesFromFourPipesViaTeeMerge() 
  	{
		// create a message to send on pipe 1
   		$pipe1Message = new Message( Message::NORMAL, (object)array( 'testProp' => 1 ) );
   		$pipe2Message = new Message( Message::NORMAL, (object)array( 'testProp' => 2 ) );
   		$pipe3Message = new Message( Message::NORMAL, (object)array( 'testProp' => 3 ) );
   		$pipe4Message = new Message( Message::NORMAL, (object)array( 'testProp' => 4 ) );

  		// create pipes 1, 2, 3 and 4
   		$pipe1 = new Pipe();
   		$pipe2 = new Pipe();
   		$pipe3 = new Pipe();
   		$pipe4 = new Pipe();
   		
  		// create merging tee
   		$teeMerge = new TeeMerge( $pipe1, $pipe2 );
   		$connectedExtraInput3 = $teeMerge->connectInput($pipe3);
   		$connectedExtraInput4 = $teeMerge->connectInput($pipe4);

		// create listener
   		$listener = new PipeListener( $this,'callBackMethod' );
   		
   		// connect the listener to the tee and write the messages
   		$connected = $teeMerge->connect($listener);
   		
   		// write messages to their respective pipes
   		$pipe1written = $pipe1->write( $pipe1Message );
   		$pipe2written = $pipe2->write( $pipe2Message );
   		$pipe3written = $pipe3->write( $pipe3Message );
   		$pipe4written = $pipe4->write( $pipe4Message );
   		
   		// test assertions
		$this->assertTrue( $pipe1Message instanceof IPipeMessage, "Expecting \$pipe1Message instanceof IPipeMessage" );
		$this->assertTrue( $pipe2Message instanceof IPipeMessage, "Expecting \$pipe2Message instanceof IPipeMessage" );
		$this->assertTrue( $pipe3Message instanceof IPipeMessage, "Expecting \$pipe3Message instanceof IPipeMessage" );
		$this->assertTrue( $pipe4Message instanceof IPipeMessage, "Expecting \$pipe4Message instanceof IPipeMessage" );
		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting \$pipe1 instanceof Pipe" );
		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe" );
		$this->assertTrue( $pipe3 instanceof Pipe, "Expecting \$pipe3 instanceof Pipe" );
		$this->assertTrue( $pipe4 instanceof Pipe, "Expecting \$pipe4 instanceof Pipe" );
		$this->assertTrue( $teeMerge instanceof TeeMerge, "Expecting teeMerge instanceof TeeMerge" );
		$this->assertTrue( $listener instanceof PipeListener, "Expecting listener instanceof PipeListener" );
   		$this->assertTrue( $connected, "Expecting connected listener to merging tee" );
   		$this->assertTrue( $connectedExtraInput3, "Expecting connected extra input \$pipe3 to merging tee" );
   		$this->assertTrue( $connectedExtraInput4, "Expecting connected extra input \$pipe4 to merging tee" );
   		$this->assertTrue( $pipe1written, "Expecting wrote message to pipe 1" );
   		$this->assertTrue( $pipe2written, "Expecting wrote message to pipe 2" );
   		$this->assertTrue( $pipe3written, "Expecting wrote message to pipe 3" );
   		$this->assertTrue( $pipe4written, "Expecting wrote message to pipe 4" );
   		
   		// test that both messages were received, then test
   		// FIFO order by inspecting the messages themselves
   		$this->assertTrue( count($this->messagesReceived) == 4, "Expecting received 4 messages" );
   		
   		// test message 1 assertions 
   		$message1 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message1 instanceof IPipeMessage, "Expecting \$message1 instanceof IPipeMessage" );
   		$this->assertTrue( $message1 === $pipe1Message, "Expecting \$message1 === \$pipe1Message" ); // object equality
   		$this->assertTrue( $message1->getType() == Message::NORMAL, "Expecting \$message1->getType() == Message::NORMAL" );
   		$this->assertTrue( $message1->getHeader()->testProp == 1, "Expecting \$message1->getHeader()->testProp == 1" );

   		// test message 2 assertions
   		$message2 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message2 instanceof IPipeMessage, "Expecting \$message2 instanceof IPipeMessage" );
   		$this->assertTrue( $message2 === $pipe2Message, "Expecting \$message2 === \$pipe2Message" ); // object equality
   		$this->assertTrue( $message2->getType() == Message::NORMAL, "Expecting \$message2->getType() == Message::NORMAL" );
   		$this->assertTrue( $message2->getHeader()->testProp == 2, "Expecting \$message2->getHeader()->testProp == 2" );

   		// test message 3 assertions 
   		$message3 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message3 instanceof IPipeMessage, "Expecting \$message3 instanceof IPipeMessage"  );
   		$this->assertTrue( $message3 === $pipe3Message, "Expecting \$message3 === \$pipe3Message"  ); // object equality
   		$this->assertTrue( $message3->getType() == Message::NORMAL, "Expecting \$message3->getType() == Message::NORMAL"  );
   		$this->assertTrue( $message3->getHeader()->testProp == 3, "Expecting \$message3->getHeader()->testProp == 3" );

   		// test message 4 assertions
   		$message4 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message4 instanceof IPipeMessage, "Expecting \$message4 instanceof IPipeMessage"  );
   		$this->assertTrue( $message4 === $pipe4Message, "Expecting \$message4 === \$pipe4Message"  ); // object equality
   		$this->assertTrue( $message4->getType() == Message::NORMAL, "Expecting \$message4->getType() == Message::NORMAL"  );
   		$this->assertTrue( $message4->getHeader()->testProp == 4, "Expecting \$message4->getHeader()->testProp == 4" );

   	}
   	
	/**
	 * Array of received messages.
	 * <P>
	 * Used by <code>callBackMedhod</code> as a place to store
	 * the recieved messages.</P>
	 */     		
   	private $messagesReceived = array();
   	
   	/**
   	 * Callback given to <code>PipeListener</code> for incoming message.
   	 * <P>
   	 * Used by <code>testReceiveMessageViaPipeListener</code> 
   	 * to get the output of pipe back into this  test to see 
   	 * that a message passes through the pipe.</P>
   	 */
   	public function callBackMethod( IPipeMessage $message)
   	{
   		array_push($this->messagesReceived, $message );
   	}
   	
}
   	