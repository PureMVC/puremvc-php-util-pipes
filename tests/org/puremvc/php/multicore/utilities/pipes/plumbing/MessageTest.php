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
	
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/Message.php';

/**
 * Test the Message class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class MessageTest extends PHPUnit_Framework_TestCase 
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
  	 * Tests the constructor parameters and getters.
  	 */
  	public function testConstructorAndGetters() 
  	{
   		// create a message with complete constructor args
  		$message = new Message( Message::NORMAL, 
   								(object)array( 'testProp' => 'testval' ),
   								simplexml_load_string('<testMessage testAtt="Hello" testAtt2="world"/>'),
   								Message::PRIORITY_HIGH);
   		
   		// test assertions
   		$this->assertTrue( $message instanceof Message, "Expecting \$message is Message" );
   		$this->assertTrue( $message->getType() == Message::NORMAL, "Expecting \$message->getType() == Message::NORMAL" );
   		$this->assertTrue( $message->getHeader()->testProp == 'testval', "Expecting \$message->getHeader()->testProp == 'testval'" );
   		$this->assertTrue( $message->getBody()->attributes()->testAtt == 'Hello', "Expecting \$message->getBody()->attributes()->testAtt == 'Hello'" );
   		$this->assertTrue( $message->getPriority() == Message::PRIORITY_HIGH, "Expecting \$message->getPriority() == Message::PRIORITY_HIGH" );
   		
   	}

  	/**
  	 * Tests message default priority.
  	 */
  	public function testDefaultPriority() 
  	{
  		// Create a message with minimum constructor args
   		$message = new Message( Message::NORMAL );
   		
   		// test assertions
   		$this->assertTrue( $message->getPriority() == Message::PRIORITY_MED, "Expecting \$message->getPriority() == Message::PRIORITY_MED" );
   		
   	}

  	/**
  	 * Tests the setters and getters.
  	 */
  	public function testSettersAndGetters() 
  	{
  		// create a message with minimum constructor args
   		$message = new Message( Message::NORMAL );
   		
   		// Set remainder via setters
   		$message->setHeader( (object)array('testProp' => 'testval' ) );
   		$message->setBody( simplexml_load_string('<testMessage testAtt="Hello" testAtt2="world"/>' ));
   		$message->setPriority( Message::PRIORITY_LOW );
   		
   		// test assertions
   		$this->assertTrue( $message instanceof Message, "Expecting \$message is Message" );
   		$this->assertTrue( $message->getType() == Message::NORMAL, "Expecting \$message->getType() == Message::NORMAL" );
   		$this->assertTrue( $message->getHeader()->testProp == 'testval', "Expecting \$message->getHeader()->testProp == 'testval'" );
   		$this->assertTrue( $message->getBody()->attributes()->testAtt == 'Hello', "Expecting \$message->getBody()->attributes()->testAtt == 'Hello'" );
   		$this->assertTrue( $message->getPriority() == Message::PRIORITY_LOW, "Expecting \$message->getPriority() == Message::PRIORITY_LOW" );
   		
   	}
}
