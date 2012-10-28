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
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/TeeSplit.php';

 /**
 * Test the TeeSplit class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class TeeSplitTest extends PHPUnit_Framework_TestCase 
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
  	 * Test connecting and disconnecting I/O Pipes.
  	 * 
  	 * <P>
  	 * Connect an input and several output pipes to a splitting tee. 
  	 * Then disconnect all outputs in LIFO order by calling disconnect 
  	 * repeatedly.</P>
  	 */
  	public function testConnectingAndDisconnectingIOPipes() 
  	{
  		// create input pipe
   		 $input = new Pipe();

  		// create output pipes 1, 2, 3 and 4
   		 $pipe1 = new Pipe();
   		 $pipe2 = new Pipe();
   		 $pipe3 = new Pipe();
   		 $pipe4 = new Pipe();

  		// create splitting tee (args are first two output fittings of tee)
   		 $teeSplit = new TeeSplit( $pipe1, $pipe2 );
   		
   		// connect 2 extra outputs for a total of 4
   		 $connectedExtra1 = $teeSplit->connect( $pipe3 );
   		 $connectedExtra2 = $teeSplit->connect( $pipe4 );

		// connect the single input
		 $inputConnected = $input->connect($teeSplit);
		
   		// test assertions
   		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting \$pipe1 instanceof Pipe" );
   		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe" );
   		$this->assertTrue( $pipe3 instanceof Pipe, "Expecting \$pipe3 instanceof Pipe" );
   		$this->assertTrue( $pipe4 instanceof Pipe, "Expecting \$pipe4 instanceof Pipe" );
   		$this->assertTrue( $teeSplit instanceof TeeSplit, "Expecting \$teeSplit instanceof TeeSplit" );
   		$this->assertTrue( $connectedExtra1, "Expecting connected pipe 3" );
   		$this->assertTrue( $connectedExtra2, "Expecting connected pipe 4" );
   		
   		// test LIFO order of output disconnection
   		$this->assertTrue( $teeSplit->disconnect() === $pipe4, "Expecting disconnected pipe 4" );
   		$this->assertTrue( $teeSplit->disconnect() === $pipe3, "Expecting disconnected pipe 3" );
   		$this->assertTrue( $teeSplit->disconnect() === $pipe2, "Expecting disconnected pipe 2" );
   		$this->assertTrue( $teeSplit->disconnect() === $pipe1, "Expecting disconnected pipe 1" );
   	}

  	/**
  	 * Test disconnectFitting method.
  	 * 
  	 * <P>
  	 * Connect several output pipes to a splitting tee. 
  	 * Then disconnect specific outputs, making sure that once
  	 * a fitting is disconnected using disconnectFitting, that
  	 * it isn't returned when disconnectFitting is called again. 
  	 * Finally, make sure that the when a message is sent to 
  	 * the tee that the correct number of output messages is
  	 * written.
  	 * </P>
  	 */
  	public function testDisconnectFitting() 
  	{
  		$this->messagesReceived = array();
  		
  		// create input pipe
		$input = new Pipe();

  		// create output pipes 1, 2, 3 and 4
		$pipe1 = new Pipe();
		$pipe2 = new Pipe();
		$pipe3 = new Pipe();
		$pipe4 = new Pipe();
		
		// setup pipelisteners 
   		$pipe1->connect( new PipeListener( $this, 'callBackMethod' ) );
   		$pipe2->connect( new PipeListener( $this, 'callBackMethod' ) );
   		$pipe3->connect( new PipeListener( $this, 'callBackMethod' ) );
   		$pipe4->connect( new PipeListener( $this, 'callBackMethod' ) );
 
  		// create splitting tee 
   		 $teeSplit = new TeeSplit( );
   		
   		// add outputs
   		$teeSplit->connect( $pipe1 );
   		$teeSplit->connect( $pipe2 );
   		$teeSplit->connect( $pipe3 );
   		$teeSplit->connect( $pipe4 );

   		// test assertions
   		$this->assertTrue( $teeSplit->disconnectFitting($pipe4) === $pipe4, "Expecting \$teeSplit.disconnectFitting(\$pipe4) === \$pipe4" );
   		$this->assertTrue( $teeSplit->disconnectFitting($pipe4) == null, "Expecting \$teeSplit.disconnectFitting(\$pipe4) == null" );
		
		// Write a message to the tee 
		$teeSplit->write(new Message(Message::NORMAL));
		
		// test $this->assertions 			
   		$this->assertTrue( count($this->messagesReceived) == 3, "Expecting messagesReceived.length == 3" );
   	}
  	
  	
  	/**
  	 * Test receiving messages from two pipes using a TeeMerge.
  	 */
  	public function testReceiveMessagesFromTwoTeeSplitOutputs() 
  	{
  		$this->messagesReceived = array();
  		
		// create a message to send on pipe 1
   		 $message = new Message( Message::NORMAL, array('testProp' => 1));
  		
  		// create output pipes 1 and 2
   		 $pipe1 = new Pipe();
   		 $pipe2 = new Pipe();

		// create and connect anonymous listeners
   		 $connected1 = $pipe1->connect( new PipeListener( $this, 'callBackMethod' ) );
   		 $connected2 = $pipe2->connect( new PipeListener( $this, 'callBackMethod' ) );
   	
  		// create splitting tee (args are first two output fittings of tee)
   		 $teeSplit = new TeeSplit( $pipe1, $pipe2 );

   		// write messages to their respective pipes
   		 $written = $teeSplit->write( $message );
   		
   		// test $this->assertions
		$this->assertTrue( $message instanceof IPipeMessage, "Expecting \$message instanceof IPipeMessage" );
		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting \$pipe1 instanceof Pipe" );
		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe" );
		$this->assertTrue( $teeSplit instanceof TeeSplit, "Expecting \$teeSplit instanceof TeeSplit" );
   		$this->assertTrue( $connected1, "Expecting connected anonymous listener to pipe 1" );
   		$this->assertTrue( $connected2, "Expecting connected anonymous listener to pipe 2" );
   		$this->assertTrue( $written, "Expecting wrote single message to tee" );
   		
   		// test that both messages were received, then test
   		// FIFO order by inspecting the messages themselves
   		$this->assertTrue(  count($this->messagesReceived) == 2, "Expecting received 2 messages" );
   		
   		// test message 1 $this->assertions 
		$message1 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message1 instanceof IPipeMessage, "Expecting \$message1 instanceof IPipeMessage" );
   		$this->assertTrue( $message1 === $message, "Expecting \$message1 === \$pipe1Message" ); // object equality

   		// test message 2 $this->assertions
		$message2 = array_shift($this->messagesReceived);
   		$this->assertTrue( $message2 instanceof IPipeMessage, "Expecting \$message1 instanceof IPipeMessage" );
   		$this->assertTrue( $message2 === $message, "Expecting \$message1 === \$pipe1Message" ); // object equality

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
   	