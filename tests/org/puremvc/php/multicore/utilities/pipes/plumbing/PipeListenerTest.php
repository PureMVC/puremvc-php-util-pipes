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
	
 /**
 * Test the PipeListener class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class PipeListenerTest extends PHPUnit_Framework_TestCase 
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
  	 * Test connecting a pipe listener to a pipe. 
  	 */
  	public function testConnectingToAPipe() 
  	{
  		// create pipe and listener
   		$pipe = new Pipe();
   		$listener = new PipeListener( $this, 'callBackMethod' );
   		
   		// connect the listener to the pipe
   		$success = $pipe->connect($listener);
   		
   		// test assertions
   		$this->assertTrue( $pipe instanceof Pipe, "Expecting pipe is Pipe" );
   		$this->assertTrue( $success, "Expecting successfully connected listener to pipe" );
   	}

  	/**
  	 * Test receiving a message from a pipe using a PipeListener.
  	 */
  	public function testReceiveMessageViaPipeListener() 
  	{
   		// create a message with complete constructor args
  		$messageToSend = new Message( Message::NORMAL,
  									  (object)array( 'testProp' => 'testval' ),
  									  simplexml_load_string('<testMessage testAtt="Hello" testAtt2="world"/>'),
  									  Message::PRIORITY_HIGH);
		
  		// create pipe and listener
   		$pipe = new Pipe();
   		$listener = new PipeListener( $this, 'callBackMethod' );
   		
   								   		
   		// connect the listener to the pipe and write the message
   		$connected = $pipe->connect($listener);
   		$written = $pipe->write( $messageToSend );
   		
   		// test assertions
   		$this->assertTrue( $pipe instanceof Pipe, "Expecting pipe is Pipe" );
   		$this->assertTrue( $connected, "Expecting successfully connected listener to pipe" );
   		$this->assertTrue( $written, "Expecting wrote message to pipe" );
   		
   		$this->assertTrue( $this->messagesReceived instanceof Message,"Expecting \$this->messagesReceived instance of Message" );
   		$this->assertTrue( $this->messagesReceived->getType() == Message::NORMAL, "Expecting \$this->messagesReceived->getType() == Message::NORMAL" );
   		$this->assertTrue( $this->messagesReceived->getHeader()->testProp == 'testval', "Expecting \$this->messagesReceived->getHeader()->testProp == 'testval'" );
   		$this->assertTrue( $this->messagesReceived->getBody()->attributes()->testAtt == 'Hello', "Expecting \$this->messagesReceived->getBody()->attributes()->testAtt == 'Hello'" );
   		$this->assertTrue( $this->messagesReceived->getPriority() == Message::PRIORITY_HIGH, "Expecting \$this->messagesReceived->getPriority() == Message::PRIORITY_HIGH" );
  		
   	}
   	
	/**
	 * Array of received messages.
	 * <P>
	 * Used by <code>callBackMedhod</code> as a place to store
	 * the recieved messages.</P>
	 */     		
   	private $messagesReceived = null;
   	
   	/**
   	 * Callback given to <code>PipeListener</code> for incoming message.
   	 * <P>
   	 * Used by <code>testReceiveMessageViaPipeListener</code> 
   	 * to get the output of pipe back into this  test to see 
   	 * that a message passes through the pipe.</P>
   	 */
   	public function callBackMethod( IPipeMessage $message)
   	{
   		$this->messagesReceived = $message;
   	}
   	
}
   	