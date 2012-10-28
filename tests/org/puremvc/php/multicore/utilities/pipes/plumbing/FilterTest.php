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


/**
 * Test the Filter class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class FilterTest extends PHPUnit_Framework_TestCase 
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
  	 * Test connecting input and output pipes to a filter as well as disconnecting the output.
  	 */
  	public function testConnectingAndDisconnectingIOPipes() 
  	{

  		// create output pipes 1
   		$pipe1 = new Pipe();
   		$pipe2 = new Pipe();

  		// create filter
   		$filter = new Filter( 'TestFilter' );
   		
   		// connect input fitting
   		$connectedInput = $pipe1->connect( $filter );
   		
   		// connect output fitting
   		$connectedOutput = $filter->connect( $pipe2 );
   		
   		// test assertions
   		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting pipe1 instanceof Pipe" );
   		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting pipe2 instanceof Pipe");
   		$this->assertTrue( $filter instanceof Filter, "Expecting filter instanceof Filter" );
   		$this->assertTrue( $connectedInput, "Expecting connected input" );
   		$this->assertTrue( $connectedOutput, "Expecting connected output" );
   		
   		// disconnect pipe 2 from filter
   		$disconnectedPipe = $filter->disconnect();
   		$this->assertTrue( $disconnectedPipe === $pipe2, "Expecting disconnected pipe2 from filter" );
   	}
  	
  	
  	/**
  	 * Test applying filter to a normal message.
  	 */
  	public function testFilteringNormalMessage() 
  	{
		// create messages to send to the queue
   		$message = new Message( Message::NORMAL, (object)array( "width" => 10, "height" =>2 ) );
  		
  		// create filter, attach an anonymous listener to the filter output to receive the message,
  		// pass in an anonymous function an parameter object
   		$filter = new Filter( 'scale', 
   							  new PipeListener( $this ,'callBackMethod' ),
   							  create_function( '$message, $params',' 
   							  	$message->getHeader()->width *= $params->factor; 
   							  	$message->getHeader()->height *= $params->factor;  
   							  '),
   							  (object)array( "factor" => 10 )
   							);

		// write messages to the filter
   		$written = $filter->write( $message );
   		
   		// test assertions
		$this->assertTrue( $message instanceof IPipeMessage, "Expecting message instanceof IPipeMessage"  );
		$this->assertTrue( $filter instanceof Filter, "Expecting filter instanceof Filter" );
   		$this->assertTrue( $written, "Expecting wrote message to filter"  );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );

   		// test filtered message assertions 
   		$recieved = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved instanceof IPipeMessage, "Expecting recieved instanceof IPipeMessage" );
   		$this->assertTrue( $recieved === $message, "Expecting recieved === message" ); // object equality
   		$this->assertTrue( $recieved->getHeader()->width == 100,  "Expecting recieved.getHeader().width == 100" ); 
   		$this->assertTrue( $recieved->getHeader()->height == 20, "Expecting recieved.getHeader().height == 20" );
   	}

   	/**
  	 * Test setting filter to bypass mode, writing, then setting back to filter mode and writing. 
  	 */
  	public function testBypassAndFilterModeToggle() 
  	{
		// create messages to send to the queue
   		$message = new Message( Message::NORMAL, (object)array( "width" => 10, "height" =>2 ) );
  		
  		// create filter, attach an anonymous listener to the filter output to receive the message,
  		// pass in an anonymous function an parameter object
   		$filter = new Filter( 'scale', 
   							  new PipeListener( $this ,'callBackMethod' ),
   							  create_function( '$message, $params',' 
   							  	$message->getHeader()->width *= $params->factor; 
   							  	$message->getHeader()->height *= $params->factor;  
   							  '),
   							  (object)array( "factor" => 10 )
   							);

   				
		// create bypass control message	
		$bypassMessage = new FilterControlMessage(FilterControlMessage::BYPASS, 'scale');

		// write bypass control message to the filter
		//var_dump($filter);
   		$bypassWritten = $filter->write( $bypassMessage );
   		
   		// write normal message to the filter
   		$written1 = $filter->write( $message );
   		
   		// test assertions
		$this->assertTrue( $message instanceof IPipeMessage, "Expecting message instanceof IPipeMessage"  );
		$this->assertTrue( $filter instanceof Filter, "Expecting filter instanceof Filter" );
   		$this->assertTrue( $bypassWritten, "Expecting wrote bypass message to filter" );
   		$this->assertTrue( $written1, "Expecting wrote normal message to filter" );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );
   		

   		// test filtered message assertions (no change to message)
   		
   		$recieved1 = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved1 instanceof IPipeMessage, "Expecting recieved instanceof IPipeMessage" );
   		$this->assertTrue( $recieved1 === $message, "Expecting recieved === message" ); // object equality
   		$this->assertTrue( $recieved1->getHeader()->width == 10,  "Expecting recieved.getHeader().width == 10" ); 
   		$this->assertTrue( $recieved1->getHeader()->height == 2, "Expecting recieved.getHeader().height == 2" );
   		
		// create filter control message	
		$filterMessage = new FilterControlMessage(FilterControlMessage::FILTER, 'scale');

   		// write bypass control message to the filter
   		$filterWritten = $filter->write( $filterMessage );
   		
   		// write normal message to the filter again
   		$written2 = $filter->write( $message );

		// test assertions   			
   		$this->assertTrue( $bypassWritten, "Expecting wrote bypass message to filter" );
   		$this->assertTrue( $written1 ,"Expecting wrote normal message to filter" );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );
   		
   		// test filtered message assertions (message filtered)
   		$recieved2 = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved2 instanceof IPipeMessage, "Expecting recieved instanceof IPipeMessage" );
   		$this->assertTrue( $recieved2 === $message, "Expecting recieved === message" ); // object equality
   		$this->assertTrue( $recieved2->getHeader()->width == 100,  "Expecting recieved.getHeader().width == 100" ); 
   		$this->assertTrue( $recieved2->getHeader()->height == 20, "Expecting recieved.getHeader().height == 20" );
   	}
   	
  	/**
  	 * Test setting filter parameters by sending control message. 
  	 */
  	public function testSetParamsByControlMessage() 
  	{
		// create messages to send to the queue
   		$message = new Message( Message::NORMAL, (object)array( "width" => 10, "height" =>2 ) );
  		
  		// create filter, attach an anonymous listener to the filter output to receive the message,
  		// pass in an anonymous function an parameter object
   		$filter = new Filter( 'scale', 
   							  new PipeListener( $this ,'callBackMethod' ),
   							  create_function( '$message, $params',' 
   							  	$message->getHeader()->width *= $params->factor; 
   							  	$message->getHeader()->height *= $params->factor;  
   							  '),
   							  (object)array( "factor" => 10 )
   							);

   				
		// create setParams control message	
		$setParamsMessage = new FilterControlMessage(FilterControlMessage::SET_PARAMS, 'scale', null, (object)array('factor'=>5) );

   		// write filter control message to the filter
   		$setParamsWritten = $filter->write( $setParamsMessage );
   		
   		// write normal message to the filter
   		$written = $filter->write( $message );
   		
   		// test assertions
		$this->assertTrue( $message instanceof IPipeMessage, "Expecting message instanceof IPipeMessage"  );
		$this->assertTrue( $filter instanceof Filter, "Expecting filter instanceof Filter" );
   		$this->assertTrue( $setParamsWritten, "Expecting wrote set_params message to filter" );
   		$this->assertTrue( $written, "Expecting wrote normal message to filter" );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );
   	
   		// test filtered message assertions (message filtered with overridden parameters)
   		$recieved = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved instanceof IPipeMessage, "Expecting recieved instanceof IPipeMessage" );
   		$this->assertTrue( $recieved === $message, "Expecting recieved === message" ); // object equality
   		$this->assertTrue( $recieved->getHeader()->width == 50,  "Expecting recieved.getHeader().width == 50" ); 
   		$this->assertTrue( $recieved->getHeader()->height == 10, "Expecting recieved.getHeader().height == 10" );

  	}
   	
  	/**
  	 * Test setting filter function by sending control message. 
  	 */
  	public function testSetFilterByControlMessage() 
  	{
		// create messages to send to the queue
   		$message = new Message( Message::NORMAL, (object)array( "width" => 10, "height" =>2 ) );
  		
  		// create filter, attach an anonymous listener to the filter output to receive the message,
  		// pass in an anonymous function an parameter object
   		$filter = new Filter( 'scale', 
   							  new PipeListener( $this ,'callBackMethod' ),
   							  create_function( '$message, $params',' 
   							  	$message->getHeader()->width *= $params->factor; 
   							  	$message->getHeader()->height *= $params->factor;  
   							  '),
   							  (object)array( "factor" => 10 )
   							);
  				
		// create setFilter control message	
		$setFilterMessage = new FilterControlMessage(FilterControlMessage::SET_FILTER, 'scale', function( $message, $params ){ $message->getHeader()->width /= $params->factor; $message->getHeader()->height /= $params->factor;  } );

   		// write filter control message to the filter
   		$setFilterWritten = $filter->write( $setFilterMessage );
   		
   		// write normal message to the filter
   		$written = $filter->write( $message );
   		
   		// test assertions
		$this->assertTrue( $message instanceof IPipeMessage, "Expecting message instanceof IPipeMessage"  );
		$this->assertTrue( $filter instanceof Filter, "Expecting filter instanceof Filter" );
   		$this->assertTrue( $setFilterWritten, "Expecting wrote set_params message to filter" );
   		$this->assertTrue( $written, "Expecting wrote normal message to filter" );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );
   	
   		// test filtered message assertions (message filtered with overridden filter function)
   		$recieved = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved instanceof IPipeMessage, "Expecting recieved instanceof IPipeMessage" );
   		$this->assertTrue( $recieved === $message, "Expecting recieved === message" ); // object equality
   		$this->assertTrue( $recieved->getHeader()->width == 1,  "Expecting recieved.getHeader().width == 1" ); 
   		$this->assertTrue( $recieved->getHeader()->height == .2, "Expecting recieved.getHeader().height == .2" );

   	}
   	
  	/**
  	 * Test using a filter function to stop propagation of a message. 
  	 * <P>
  	 * The way to stop propagation of a message from within a filter
  	 * is to throw an error from the filter function. This test creates
  	 * two NORMAL messages, each with header objects that contain 
  	 * a <code>bozoLevel</code> property. One has this property set to 
  	 * 10, the other to 3.</P>
  	 * <P>
  	 * Creates a Filter, named 'bozoFilter' with an anonymous pipe listener
  	 * feeding the output back into this test. The filter funciton is an 
  	 * anonymous function that throws an error if the message's bozoLevel 
  	 * property is greater than the filter parameter <code>bozoThreshold</code>.
  	 * the anonymous filter parameters object has a <code>bozoThreshold</code>
  	 * value of 5.</P>
  	 * <P>
  	 * The messages are written to the filter and it is shown that the 
  	 * message with the <code>bozoLevel</code> of 10 is not written, while
  	 * the message with the <code>bozoLevel</code> of 3 is.</P> 
  	 */
  	public function testUseFilterToStopAMessage() 
  	{
		// create messages to send to the queue
   		$message1 = new Message( Message::NORMAL, (object)array( 'bozoLevel' => 10, 'user' => 'Dastardly Dan') );
   		$message2 = new Message( Message::NORMAL, (object)array( 'bozoLevel' => 3, 'user' => 'Dudley Doright') );
  		
  		// create filter, attach an anonymous listener to the filter output to receive the message,
  		// pass in an anonymous function and an anonymous parameter object
   		$filter = new Filter( 'bozoFilter', 
   							  new PipeListener( $this ,'callBackMethod' ),
   							  create_function( 
   							  		'$message, $params',
   							  		'if ($message->getHeader()->bozoLevel > $params->bozoThreshold)
   							  		 {
   							  		 	 throw new Exception(\'bozoFiltered\');
   							  		 }
   							  		'),
   							  (object)array('bozoThreshold' =>5) 
   						    );
		
   		// write normal message to the filter
   		$written1 = $filter->write( $message1 );
   		$written2 = $filter->write( $message2 );
   		
   		// test assertions
		$this->assertTrue( $message1 instanceof IPipeMessage, "Expecting \$message1 instanceof IPipeMessage"  );
		$this->assertTrue( $message2 instanceof IPipeMessage, "Expecting \$message2 instanceof IPipeMessage"  );
		$this->assertTrue( $filter instanceof Filter, "Expecting \$filter instanceof Filter" );
   		$this->assertTrue( $written1 == false, "Expecting failed to write bad message" );
   		$this->assertTrue( $written2 == true, "Expecting wrote good message" );
   		$this->assertTrue( count($this->messagesReceived) == 1, "Expecting received 1 messages" );
   		
   		// test filtered message assertions (message with good auth token passed
   		$recieved = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved instanceof IPipeMessage, "Expecting recieved instanceof IPipeMessage" );
   		$this->assertTrue( $recieved === $message2, "Expecting \$recieved === \$message2" ); // object equality

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
   	 *  
   	 */
   	public function callBackMethod( IPipeMessage $message )
   	{
   		array_push( $this->messagesReceived, $message );
   	}
   	
}
