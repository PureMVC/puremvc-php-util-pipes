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
	
 /**
 * Test the Pipe class.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class PipeTest extends PHPUnit_Framework_TestCase 
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
  	 * Test the constructor.
  	 */
  	public function testConstructor() 
  	{
   		$pipe = new Pipe();
   		
   		// test assertions
   		$this->assertTrue( $pipe instanceof Pipe, "Expecting \$pipe instanceof Pipe" );
   	}

  	/**
  	 * Test connecting and disconnecting two pipes. 
  	 */
  	public function testConnectingAndDisconnectingTwoPipes() 
  	{
  		// create two pipes
   		$pipe1 = new Pipe();
   		$pipe2 = new Pipe();
   		// connect them
   		$success = $pipe1->connect($pipe2);
   		
   		// test assertions
   		$this->assertTrue( $pipe1 instanceof Pipe, "Expecting \$pipe1 instanceof Pipe" );
   		$this->assertTrue( $pipe2 instanceof Pipe, "Expecting \$pipe2 instanceof Pipe" );
   		$this->assertTrue( $success, "Expecting connected pipe1 to pipe2" );
   		
   		// disconnect pipe 2 from pipe 1
   		$disconnectedPipe = $pipe1->disconnect();
   		$this->assertTrue( $disconnectedPipe === $pipe2, "Expecting disconnected pipe2 from pipe1" );
		
   	}
   	
  	/**
  	 * Test attempting to connect a pipe to a pipe with an output already connected. 
  	 */
  	public function testConnectingToAConnectedPipe() 
  	{
  		// create two pipes
   		$pipe1 = new Pipe();
   		$pipe2 = new Pipe();
   		$pipe3 = new Pipe();

   		// connect them
   		$success = $pipe1->connect($pipe2);
   		
   		// test assertions
   		$this->assertTrue( $success, "Expecting connected pipe1 to pipe2" );
   		$this->assertTrue( $pipe1->connect($pipe3) == false, "Expecting can't connect pipe3 to pipe1" );
		
   	}
   	
}
