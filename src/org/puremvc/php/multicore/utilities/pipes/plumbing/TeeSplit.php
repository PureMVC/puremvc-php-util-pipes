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
 * Splitting Pipe Tee.
 * <P>
 * Writes input messages to multiple output pipe fittings.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
class TeeSplit implements IPipeFitting
{
	protected $outputs = array();
	
	/**
	 * Constructor.
	 * <P>
	 * Create the TeeSplit and connect the up two optional outputs.
	 * This is the most common configuration, though you can connect
	 * as many outputs as necessary by calling <code>connect</code>.</P>
	 */
	public function TeeSplit( IPipeFitting $output1=null, IPipeFitting $output2=null ) 
	{
		if (!is_null($output1))
		{ 
			$this->connect($output1);
		}
		
		if (!is_null($output2))
		{ 
			$this->connect($output2);
		}
		
	}

	/** 
	 * Connect the output IPipeFitting.
	 * <P>
	 * NOTE: You can connect as many outputs as you want
	 * by calling this method repeatedly.</P>
	 * 
	 * @param IPipeFitting $output the IPipeFitting to connect for output.
	 * @return bool
	 */
	public function connect( IPipeFitting $output )
	{
		array_push($this->outputs,$output);
		return true;
	}
	
	/** 
	 * Disconnect the most recently connected output fitting. (LIFO)
	 * <P>
	 * To disconnect all outputs, you must call this 
	 * method repeatedly untill it returns null.</P>
	 * 
	 * @return IPipeFitting
	 */
	public function disconnect( ) 
	{
		return array_pop($this->outputs);
	}

	/** 
	 * Disconnect a given output fitting. 
	 * <P>
	 * If the fitting passed in is connected
	 * as an output of this <code>TeeSplit</code>, then
	 * it is disconnected and the reference returned.</P>
	 * <P>
	 * If the fitting passed in is not connected as an 
	 * output of this <code>TeeSplit</code>, then <code>null</code>
	 * is returned.</P>
	 * 
	 * @param IPipeFitting $target the IPipeFitting to connect for output.
	 * @return IPipeFitting
	 */
	public function disconnectFitting( IPipeFitting  $target ) 
	{
		$removed = null;
		
		for ($i=0; $i<count($this->outputs);$i++)
		{
			$output = $this->outputs[$i];
			if ($output === $target) 
			{
				array_splice($this->outputs,$i,1);	
				$removed = $output;
				break;
			}
		}
		return $removed;
	}

	/**
	 * Write the message to all connected outputs.
	 * <P>
	 * Returns false if any output returns false, 
	 * but all outputs are written to regardless.</P>
	 * @param IPipeMessage $message the message to write
	 * @return bool Boolean whether any connected outputs failed
	 */
	public function write( IPipeMessage $message )
	{
		$success = true;
		
		foreach($this->outputs as $output)
		{
			if (!$output->write( $message ) )
			{ 
				$success = false;
			}
		}
		
		return $success;
					
	}
	
}
