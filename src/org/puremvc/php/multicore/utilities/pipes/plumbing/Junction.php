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
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/PipeListener.php';
/**
 * Pipe Junction.
 * 
 * <P>
 * Manages Pipes for a Module. 
 * 
 * <P>
 * When you register a Pipe with a Junction, it is 
 * declared as being an INPUT pipe or an OUTPUT pipe.</P> 
 * 
 * <P>
 * You can retrieve or remove a registered Pipe by name, 
 * check to see if a Pipe with a given name exists,or if 
 * it exists AND is an INPUT or an OUTPUT Pipe.</P> 
 * 
 * <P>
 * You can send an <code>IPipeMessage</code> on a named INPUT Pipe 
 * or add a <code>PipeListener</code> to registered INPUT Pipe.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
class Junction
{
	/**
	 * INPUT Pipe Type
	 */
	const INPUT = 'input';

	/**
	 * OUTPUT Pipe Type
	 */
	const OUTPUT = 'output';
	
	/**
	 * The names of the INPUT pipes
	 * @var array
	 */
	protected $inputPipes = array();
	
	/**
	 * The names of the OUTPUT pipes
	 * @var array
	 */
	protected $outputPipes = array();
	
	/** 
	 * The map of pipe names to their pipes
	 * @var array
	 */
	protected $pipesMap = array();
	
	/**
	 * The map of pipe names to their types
	 * @var array
	 */
	protected $pipeTypesMap = array();

	// Constructor. 
	public function __construct( )
	{
	}

	/**
	 * Register a pipe with the junction.
	 * <P>
	 * Pipes are registered by unique name and type,
	 * which must be either <code>Junction.INPUT</code>
	 * or <code>Junction.OUTPUT</code>.</P>
 	 * <P>
	 * NOTE: You cannot have an INPUT pipe and an OUTPUT
	 * pipe registered with the same name. All pipe names
	 * must be unique regardless of type.</P>
	 * 
	 * @param string $name
	 * @param string $type
	 * @param IPipeFitting $pipe
	 * @return bool true if successfully registered. false if another pipe exists by that name.
	 */
	public function registerPipe( $name, $type, IPipeFitting $pipe )
	{ 
		$success = true;
		if ( !isset($this->pipesMap[$name]) )
		{
			$this->pipesMap[$name] = $pipe;
			$this->pipeTypesMap[$name] = $type;
			switch ($type) {
				case Junction::INPUT:
					array_push($this->inputPipes,$name);	
					break;						
				case Junction::OUTPUT:
					array_push($this->outputPipes,$name);	
					break;					
				default:	
					$success = false;
			}
		} 
		else 
		{
			$success = false;
		}
		
		return $success;
	}
	
	/**
	 * Does this junction have a pipe by this name?
	 * 
	 * @param string $name the pipe to check for 
	 * @return Boolean whether as pipe is registered with that name.
	 */ 
	public function hasPipe( $name )
	{
		return isset($this->pipesMap[ $name ]);
	}
	
	/**
	 * Does this junction have an INPUT pipe by this name?
	 * 
	 * @param string $name the pipe to check for 
	 * @return bool Whether an INPUT pipe is registered with that name.
	 */ 
	public function hasInputPipe( $name )
	{
		return ( $this->hasPipe($name) && ($this->pipeTypesMap[$name] == Junction::INPUT) );
	}

	/**
	 * Does this junction have an OUTPUT pipe by this name?
	 * 
	 * @param string $name the pipe to check for 
	 * @return bool Whether an OUTPUT pipe is registered with that name.
	 */ 
	public function hasOutputPipe( $name )
	{
		return ( $this->hasPipe($name) && ($this->pipeTypesMap[$name] == Junction::OUTPUT) );
	}

	/**
	 * Remove the pipe with this name if it is registered.
	 * <P>
	 * NOTE: You cannot have an INPUT pipe and an OUTPUT
	 * pipe registered with the same name. All pipe names
	 * must be unique regardless of type.</P>
	 * 
	 * @param string $name the pipe to remove
	 * @retuirn void
	 */
	public function removePipe( $name ) 
	{
		if ( $this->hasPipe($name) ) 
		{
			 $type = $this->pipeTypesMap[$name];
			 $pipesList = array();
			switch ($type) {
				case Junction::INPUT:
					$pipesList = $this->inputPipes;
					break;						
				case Junction::OUTPUT:
					$pipesList = $this->outputPipes;	
					break;					
			}
			for ( $i=0; $i<count($pipesList); $i++)
			{
				if ($pipesList[$i] == $name)
				{
					array_slice($pipesList,$i,1);
					break;
				}
			}
			unset($this->pipesMap[$name]);
			unset($this->pipeTypesMap[$name]);
		}
	}

	/**
	 * Retrieve the named pipe.
	 * 
	 * @param string $name the pipe to retrieve
	 * @return IPipeFitting the pipe registered by the given name if it exists
	 */
	public function retrievePipe( $name ) 
	{
		return (isset($this->pipesMap[$name]) ? $this->pipesMap[$name] : null);
	}

	/**
	 * Add a PipeListener to an INPUT pipe.
	 * <P>
	 * NOTE: there can only be one PipeListener per pipe,
	 * and the listener function must accept an IPipeMessage
	 * as its sole argument.</P> 
	 * 
	 * @param string $inputPipeName the INPUT pipe to add a PipeListener to
	 * @param object $context the calling context or 'this' object  
	 * @param string $listener the function name on the context to call
	 * @return bool
	 */
	public function addPipeListener( $inputPipeName, $context, $listener ) 
	{
		$success = false;
		if ( $this->hasInputPipe($inputPipeName) )
		{
			$pipe = $this->pipesMap[$inputPipeName];
			$success = $pipe->connect( new PipeListener($context, $listener) );
		} 
		return $success;
	}
	
	/**
	 * Send a message on an OUTPUT pipe.
	 * 
	 * @param string $outputPipeName the OUTPUT pipe to send the message on
	 * @param IPipeMessage $message the IPipeMessage to send  
	 * @return bool
	 */
	public function sendMessage( $outputPipeName, IPipeMessage $message ) 
	{
		$success = false;
		if ( $this->hasOutputPipe($outputPipeName) )
		{
			$pipe = $this->pipesMap[$outputPipeName];
			$success = $pipe->write($message);
		} 
		return $success;
	}

}

