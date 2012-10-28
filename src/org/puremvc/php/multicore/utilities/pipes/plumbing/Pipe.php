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
 * Pipe.
 * <P>
 * This is the most basic <code>IPipeFitting</code>,
 * simply allowing the connection of an output
 * fitting and writing of a message to that output.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */	
class Pipe implements IPipeFitting
{
	protected $output;

	/**
	 * 
	 * @param IPipeFitting $output
	 * @return Pipe
	 */
	public function __construct( IPipeFitting $output=null )
	{
		if (!is_null($output))
		{
			$this->connect($output);
		}
	}

	/**
	 * Connect another PipeFitting to the output.
	 * 
	 * PipeFittings connect to and write to other 
	 * PipeFittings in a one-way, syncrhonous chain.</P>
	 * 
	 * @param IPipeFitting $%output
	 * @return bool Boolean true if no other fitting was already connected.
	 */
	public function connect( IPipeFitting $output )
	{
		$success = false;
		if (!isset($this->output)) 
		{
			$this->output = $output;
			$success = true;
		}
		return $success;
	}
	
	/**
	 * Disconnect the Pipe Fitting connected to the output.
	 * <P>
	 * This disconnects the output fitting, returning a 
	 * reference to it. If you were splicing another fitting
	 * into a pipeline, you need to keep (at least briefly) 
	 * a reference to both sides of the pipeline in order to 
	 * connect them to the input and output of whatever 
	 * fiting that you're splicing in.</P>
	 * 
	 * @return IPipeFitting the now disconnected output fitting
	 */
	public function disconnect(  )
	{
		$disconnectedFitting = $this->output;
		unset($this->output);
		return $disconnectedFitting;
	}
	
	/**
	 * Write the message to the connected output.
	 * 
	 * @param IPipeMessage $message the message to write
	 * @return Boolean whether any connected downpipe outputs failed
	 */
	public function write( IPipeMessage $message )
	{
		return $this->output->write( $message );
	}
	
}
