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
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/FilterControlMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/Message.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Pipe.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Filter.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/PipeListener.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Junction.php';

 /**
 * Test the Junction class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class JunctionTest extends PHPUnit_Framework_TestCase 
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
  	 * Test registering an INPUT pipe to a junction.
  	 * <P>
  	 * Tests that the INPUT pipe is successfully registered and
  	 * that the hasPipe and hasInputPipe methods work. Then tests
  	 * that the pipe can be retrieved by name.</P>
  	 * <P>
  	 * Finally, it removes the registered INPUT pipe and tests
  	 * that all the previous assertions about it's registration
  	 * and accessability via the Junction are no longer true.</P>
  	 */
  	public function testRegisterRetrieveAndRemoveInputPipe() 
  	{
  		// create pipe connected to this test with a pipelistener
   		$pipe = new Pipe( );
		
		// create junction
		$junction = new Junction();

		// register the pipe with the junction, giving it a name and direction
		$registered = $junction->registerPipe( 'testInputPipe', Junction::INPUT, $pipe );
		
   		// test assertions
   		$this->assertTrue( $pipe instanceof Pipe, "Expecting \$pipe instanceof Pipe" );
   		$this->assertTrue( $junction instanceof Junction, "Expecting \$junction instanceof Junction" );
   		$this->assertTrue( $registered, "Expecting regsitered pipe" );
		
   		// assertions about junction methods once input  pipe is registered
   		$this->assertTrue( $junction->hasPipe('testInputPipe'), "Expecting junction has pipe" );
   		$this->assertTrue( $junction->hasInputPipe('testInputPipe'), "Expecting junction has pipe registered as an INPUT type" );
   		$this->assertTrue( $junction->retrievePipe('testInputPipe') === $pipe, "Expecting pipe retrieved from junction"); // object equality

   		// now remove the pipe and be sure that it is no longer there (same assertions should be false)
   		$junction->removePipe('testInputPipe');
   		$this->assertFalse( $junction->hasPipe('testInputPipe'), "Expecting junction has pipe" );
   		$this->assertFalse( $junction->hasInputPipe('testInputPipe'), "Expecting junction has pipe registered as an INPUT type" );
   		$this->assertFalse( $junction->retrievePipe('testInputPipe') === $pipe, "Expecting pipe retrieved from junction"); // object equality
   		
   	}
	
  	/**
  	 * Test registering an OUTPUT pipe to a junction.
  	 * <P>
  	 * Tests that the OUTPUT pipe is successfully registered and
  	 * that the hasPipe and hasOutputPipe methods work. Then tests
  	 * that the pipe can be retrieved by name.</P>
   	 * <P>
  	 * Finally, it removes the registered OUTPUT pipe and tests
  	 * that all the previous assertions about it's registration
  	 * and accessability via the Junction are no longer true.</P>
 	 */
	public function testRegisterRetrieveAndRemoveOutputPipe() 
  	{
  		// create pipe connected to this test with a pipelistener
   		$pipe = new Pipe( );
		
		// create junction
		$junction = new Junction();

		// register the pipe with the junction, giving it a name and direction
		$registered = $junction->registerPipe( 'testOutputPipe', Junction::OUTPUT, $pipe );
		
   		// test assertions
   		$this->assertTrue( $pipe instanceof Pipe, "Expecting \$pipe instanceof Pipe" );
   		$this->assertTrue( $junction instanceof Junction, "Expecting \$junction instanceof Junction" );
   		$this->assertTrue( $registered, "Expecting regsitered pipe" );
		
   		// assertions about junction methods once input  pipe is registered
   		$this->assertTrue( $junction->hasPipe('testOutputPipe'), "Expecting junction has pipe" );
   		$this->assertTrue( $junction->hasOutputPipe('testOutputPipe'), "Expecting junction has pipe registered as an OUTPUT type" );
   		$this->assertTrue( $junction->retrievePipe('testOutputPipe') === $pipe, "Expecting pipe retrieved from junction"); // object equality

   		// now remove the pipe and be sure that it is no longer there (same assertions should be false)
   		$junction->removePipe('testOutputPipe');
   		$this->assertFalse( $junction->hasPipe('testOutputPipe'), "Expecting junction has pipe" );
   		$this->assertFalse( $junction->hasOutputPipe('testOutputPipe'), "Expecting junction has pipe registered as an INPUT type" );
   		$this->assertFalse( $junction->retrievePipe('testOutputPipe') === $pipe, "Expecting pipe can't be retrieved from junction"); // object equality
		
   	}
	
	/**
  	 * Test adding a PipeListener to an Input Pipe.
  	 * <P>
  	 * Registers an INPUT Pipe with a Junction, then tests
  	 * the Junction's addPipeListener method, connecting
  	 * the output of the pipe back into to the test. If this
  	 * is successful, it sends a message down the pipe and 
  	 * checks to see that it was received.</P>
 	 */
  	public function testAddingPipeListenerToAnInputPipe() 
  	{
  		// create pipe 
   		$pipe = new Pipe();
		
		// create junction
		$junction = new Junction();

		// create test message
		$message = new Message(Message::NORMAL, array('testVal' => 1));
		
		// register the pipe with the junction, giving it a name and direction
		$registered = $junction->registerPipe( 'testInputPipe', Junction::INPUT, $pipe );

		// add the pipelistener using the junction method
		$listenerAdded = $junction->addPipeListener('testInputPipe', $this, 'callBackMethod');
					
		// send the message using our reference to the pipe, 
		// it should show up in messageReceived property via the pipeListener
		$sent = $pipe->write($message); 
		
		// test assertions
   		$this->assertTrue( $pipe instanceof Pipe, "Expecting \$pipe instanceof Pipe" );
   		$this->assertTrue( $junction instanceof Junction, "Expecting \$junction instanceof Junction" );
   		$this->assertTrue( $registered, "Expecting regsitered pipe" );
   		$this->assertTrue( $listenerAdded, "Expecting added pipeListener" );
   		$this->assertTrue( $sent, "Expecting successful write to pipe" );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );
   		$this->assertTrue( array_pop($this->messagesReceived) === $message, "Expecting received message was same instance sent" ); //object equality
		   			   			
   	}
   	
  	/**
  	 * Test using sendMessage on an OUTPUT pipe.
  	 * <P>
  	 * Creates a Pipe, Junction and Message. 
  	 * Adds the PipeListener to the Pipe.
  	 * Adds the Pipe to the Junction as an OUTPUT pipe.
  	 * uses the Junction's sendMessage method to send
  	 * the Message, then checks that it was received.</P>
 	 */
  	public function testSendMessageOnAnOutputPipe() 
  	{
   		// create pipe 
   		$pipe = new Pipe( );
		
		// add a PipeListener manually 
		$listenerAdded = $pipe->connect(new PipeListener($this, 'callBackMethod'));
					
		// create junction
		$junction = new Junction();

		// create test message
		$message = new Message(Message::NORMAL, array('testVal' => 1));
		
		// register the pipe with the junction, giving it a name and direction
		$registered = $junction->registerPipe( 'testOutputPipe', Junction::OUTPUT, $pipe );

		// send the message using the Junction's method 
		// it should show up in messageReceived property via the pipeListener
		$sent = $junction->sendMessage('testOutputPipe', $message);
		
		// test assertions
   		$this->assertTrue( $pipe instanceof Pipe, "Expecting \$pipe instanceof Pipe" );
   		$this->assertTrue( $junction instanceof Junction, "Expecting \$junction instanceof Junction" );
   		$this->assertTrue( $registered, "Expecting regsitered pipe" );
   		$this->assertTrue( $listenerAdded, "Expecting added pipeListener" );
   		$this->assertTrue( $sent, "Expecting successful write to pipe" );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );
   		$this->assertTrue( array_pop($this->messagesReceived) === $message, "Expecting received message was same instance sent" ); //object equality

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
