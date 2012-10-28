<?php
/**
 * PureMVC PHP MultiCore Pipes Utility 
 * 
 * A PHP port of Cliff Hall
 * PureMVC AS3/MultiCore Pipes Utility
 * 
 * Created on Jully 29, 2009
 * 
 * @version 1.0
 * @author Michel Chouinard <michel.chouinard@gmail.com>
 * @copyright PureMVC - Copyright(c) 2006-2008 Futurescale, Inc., Some rights reserved.
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */

/**
 * 
 */
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeFitting.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeMessage.php';

/**
 * Pipe Listener.
 * <P>
 * Allows a class that does not implement <code>IPipeFitting</code> to
 * be the final recipient of the messages in a pipeline.</P>
 * 
 * @see Junction
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */ 
class PipeListener implements IPipeFitting
{
	/**
	 * @var object
	 */
	private $context;
	
	/**
	 * @var string
	 */
	private $listener;
	
	/**
	 * 
	 * @param object $context
	 * @param string $listener
	 * @return PipeListener
	 */	
	public function __construct( $context, $listener )
	{
		$this->context = $context;
		$this->listener = $listener;
	}
	
	/**
	 *  Can't connect anything beyond this.
	 *  
	 * @param IPipeFitting $output
	 * @return bool Boolean true if no other fitting was already connected.
	 */
	public function connect( IPipeFitting $output )
	{
		return false;
	}

	/**
	 *  Can't disconnect since you can't connect, either.
	 * 
	 * @return IPipeFitting NULL
	 */
	public function disconnect()
	{
		return null;
	}

	/**
	 * Write the message to the output Pipe Fitting.
	 * <P>
	 * There may be subsequent filters and tees
 	 * (which also implement this interface), that the 
 	 * fitting is writing to, and so a message
 	 * may branch and arrive in different forms at
 	 * different endpoints. </P>
 	 * <P>
 	 * If any fitting in the chain returns false 
 	 * from this method, then the client who originally 
 	 * wrote into the pipe can take action, such as 
 	 * rolling back changes.</P>
 	 * 
 	 * @param IPipeMessage $message
 	 * @return bool
 	 */
	public function write( IPipeMessage $message)
	{
		$context = $this->context;
		$listener = $this->listener;
		
		$context->$listener($message);
		return true;
	}
}
