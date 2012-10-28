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
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/QueueControlMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Queue.php';


	
 /**
 * Test the Queue class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class QueueTest extends PHPUnit_Framework_TestCase 
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
  	 * Test connecting input and output pipes to a queue. 
  	 */
  	public function testConnectingIOPipes() 
  	{

  		// create output pipes 1
   		$pipe1 = new Pipe();
   		$pipe2 = new Pipe();

  		// create queue
   		$queue = new Queue( );
   		
   		// connect input fitting
   		$connectedInput = $pipe1->connect( $queue );
   		
   		// connect output fitting
   		$connectedOutput = $queue->connect( $pipe2 );
   		
   		// test assertions
   		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting \$pipe1 instanceof Pipe" );
   		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe" );
   		$this->assertTrue( $queue instanceof Queue, "Expecting \$queue instanceof Queue" );
  		$this->assertTrue( $connectedInput, "Expecting connected input" );
   		$this->assertTrue( $connectedOutput, "Expecting connected output" );
   	}
  	
  	
  	/**
  	 * Test writing multiple messages to the Queue followed by a Flush message.
   	 * <P>
  	 * Creates messages to send to the queue. 
  	 * Creates queue, attaching an anonymous listener to its output.
  	 * Writes messages to the queue. Tests that no messages have been
  	 * received yet (they've been enqueued). Sends FLUSH message. Tests
  	 * that messages were receieved, and in the order sent (FIFO).<P>
 	 */
  	public function testWritingMultipleMessagesAndFlush() 
  	{
  		$this->messagesReceived = array();
  		
		// create messages to send to the queue
   		$message1 = new Message( Message::NORMAL, (object)array( 'testProp' => 1 ));
   		$message2 = new Message( Message::NORMAL, (object)array( 'testProp' => 2 ));
   		$message3 = new Message( Message::NORMAL, (object)array( 'testProp' => 3 ));
  		
		// create queue control flush message
   		$flush = new QueueControlMessage( QueueControlMessage::FLUSH );

  		// create queue, attaching an anonymous listener to its output
   		$queue = new Queue( new PipeListener( $this ,'callBackMethod' ) );

   		// write messages to the queue
   		$message1written = $queue->write( $message1 );
   		$message2written = $queue->write( $message2 );
   		$message3written = $queue->write( $message3 );
   		
   		// test assertions
		$this->assertTrue( $message1 instanceof IPipeMessage, "Expecting message1 instanceof IPipeMessage" );
		$this->assertTrue( $message2 instanceof IPipeMessage, "Expecting message2 instanceof IPipeMessage" );
		$this->assertTrue( $message3 instanceof IPipeMessage, "Expecting message3 instanceof IPipeMessage" );
		$this->assertTrue( $flush instanceof IPipeMessage, "Expecting flush instanceof IPipeMessage" );
		$this->assertTrue( $queue instanceof Queue, "Expecting queue instanceof Queue" );

   		$this->assertTrue( $message1written, "Expecting wrote message1 to queue" );
   		$this->assertTrue( $message2written, "Expecting wrote message2 to queue" );
   		$this->assertTrue( $message3written, "Expecting wrote message3 to queue" );

   		// test that no messages were received (they've been enqueued)
   		$this->assertTrue( count($this->messagesReceived) == 0, "Expecting received 0 messages" );

   		// write flush control message to the queue
   		$flushWritten = $queue->write( $flush );
   		
   		// test that all messages were received, then test
   		// FIFO order by inspecting the messages themselves
   		$this->assertTrue( count($this->messagesReceived) == 3, "Expecting received 3 messages" );
   		
   		// test message 1 assertions 
   		$recieved1 = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved1 instanceof IPipeMessage, "Expecting recieved1 instanceof IPipeMessage" );
   		$this->assertTrue( $recieved1 === $message1, "Expecting recieved1 === message1" ); // object equality

   		// test message 2 assertions
   		$recieved2 = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved2 instanceof IPipeMessage, "Expecting recieved2 instanceof IPipeMessage" );
   		$this->assertTrue( $recieved2 === $message2, "Expecting recieved2 === message2" ); // object equality

   		// test message 3 assertions
   		$recieved3 = array_shift($this->messagesReceived);
   		$this->assertTrue( $recieved3 instanceof IPipeMessage, "Expecting recieved3 instanceof IPipeMessage" );
   		$this->assertTrue( $recieved3 === $message3, "Expecting recieved3 === message3" ); // object equality

   	}

  	/**
  	 * Test the Sort-by-Priority and FIFO modes.
  	 * <P>
  	 * Creates messages to send to the queue, priorities unsorted. 
  	 * Creates queue, attaching an anonymous listener to its output.
  	 * Sends SORT message to start sort-by-priority order mode.
  	 * Writes messages to the queue. Sends FLUSH message, tests
  	 * that messages were receieved in order of priority, not how
  	 * they were sent.<P>
  	 * <P>
  	 * Then sends a FIFO message to switch the queue back to
  	 * default FIFO behavior, sends messages again, flushes again,
  	 * tests that the messages were recieved and in the order they
  	 * were originally sent.</P>
  	 */
  	public function testSortByPriorityAndFIFO() 
  	{
  		$this->messagesReceived = array();
  		
  		// create messages to send to the queue
   		$message1 = new Message( Message::NORMAL,null,null,Message::PRIORITY_MED);
   		$message2 = new Message( Message::NORMAL,null,null,Message::PRIORITY_LOW);
   		$message3 = new Message( Message::NORMAL,null,null,Message::PRIORITY_HIGH);
  		
  		// create queue, attaching an anonymous listener to its output
   		$queue= new Queue( new PipeListener( $this ,'callBackMethod' ) );
   		
   		// begin sort-by-priority order mode
		$sortWritten = $queue->write(new QueueControlMessage( QueueControlMessage::SORT ));
		
   		// write messages to the queue
   		$message1written = $queue->write( $message1 );
   		$message2written = $queue->write( $message2 );
   		$message3written = $queue->write( $message3 );
		
		// flush the queue
		$flushWritten = $queue->write(new QueueControlMessage( QueueControlMessage::FLUSH ));
		   			
   		// test assertions
   		$this->assertTrue( $sortWritten, "Expecting wrote sort message to queue" );
   		$this->assertTrue( $message1written, "Expecting wrote message1 to queue" );
   		$this->assertTrue( $message2written, "Expecting wrote message2 to queue" );
   		$this->assertTrue( $message3written, "Expecting wrote message3 to queue" );
   		$this->assertTrue( $flushWritten, "Expecting wrote flush message to queue");

   		// test that 3 messages were received
   		$this->assertTrue( count($this->messagesReceived) == 3, "Expecting received 3 messages" );

   		// get the messages
   		$recieved1 = array_shift($this->messagesReceived);
   		$recieved2 = array_shift($this->messagesReceived);
   		$recieved3 = array_shift($this->messagesReceived);

   		// test that the message order is sorted 
		$this->assertTrue( $recieved1->getPriority() < $recieved2->getPriority(), "Expecting recieved1 is higher priority than recieved 2" ); 
		$this->assertTrue( $recieved2->getPriority() < $recieved3->getPriority(), "Expecting recieved2 is higher priority than recieved 3" ); 
   		$this->assertTrue( $recieved1 === $message3, "Expecting recieved1 === message3" ); // object equality
   		$this->assertTrue( $recieved2 === $message1, "Expecting recieved2 === message1" ); // object equality
   		$this->assertTrue( $recieved3 === $message2, "Expecting recieved3 === message2" ); // object equality

   		// begin FIFO order mode
		$fifoWritten = $queue->write(new QueueControlMessage( QueueControlMessage::FIFO ));

   		// write messages to the queue
   		$message1writtenAgain = $queue->write( $message1 );
   		$message2writtenAgain = $queue->write( $message2 );
   		$message3writtenAgain = $queue->write( $message3 );
		
		// flush the queue
		$flushWrittenAgain = $queue->write(new QueueControlMessage( QueueControlMessage::FLUSH ));
		   			
   		// test assertions
   		$this->assertTrue( $fifoWritten, "Expecting wrote fifo message to queue" );
   		$this->assertTrue( $message1writtenAgain, "Expecting wrote message1 to queue again" );
   		$this->assertTrue( $message2writtenAgain, "Expecting wrote message2 to queue again" );
   		$this->assertTrue( $message3writtenAgain, "Expecting wrote message3 to queue again" );
   		$this->assertTrue( $flushWrittenAgain, "Expecting wrote flush message to queue again");

   		// test that 3 messages were received 
   		$this->assertTrue( count($this->messagesReceived) == 3, "Expecting received 3 messages" );

   		// get the messages
   		$recieved1Again = array_shift($this->messagesReceived);
   		$recieved2Again = array_shift($this->messagesReceived);
   		$recieved3Again = array_shift($this->messagesReceived);

   		// test message order is FIFO
   		$this->assertTrue( $recieved1Again === $message1 , "Expecting recieved1Again === message1"); // object equality
   		$this->assertTrue( $recieved2Again === $message2, "Expecting recieved2Again === message2" ); // object equality
   		$this->assertTrue( $recieved3Again === $message3, "Expecting recieved3Again === message3" ); // object equality
		$this->assertTrue( $recieved1Again->getPriority() == Message::PRIORITY_MED, "Expecting recieved1Again is priority med " ); 
		$this->assertTrue( $recieved2Again->getPriority() == Message::PRIORITY_LOW, "Expecting recieved2Again is priority low " ); 
		$this->assertTrue( $recieved3Again->getPriority() == Message::PRIORITY_HIGH, "Expecting recieved3Again is priority high " ); 

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
   	