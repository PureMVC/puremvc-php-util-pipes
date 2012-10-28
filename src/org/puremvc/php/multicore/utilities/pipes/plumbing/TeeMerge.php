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
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Pipe.php';
	
/** 
 * Merging Pipe Tee.
 * <P>
 * Writes the messages from multiple input pipelines into
 * a single output pipe fitting.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
class TeeMerge extends Pipe
{
	/**
	 * Constructor.
	 * <P>
	 * Create the TeeMerge and the two optional constructor inputs.
	 * This is the most common configuration, though you can connect
	 * as many inputs as necessary by calling <code>connectInput</code>
	 * repeatedly.</P>
	 * <P>
	 * Connect the single output fitting normally by calling the 
	 * <code>connect</code> method, as you would with any other IPipeFitting.</P>
	 */
	public function TeeMerge( IPipeFitting $input1=null, IPipeFitting $input2=null ) 
	{
		if (!is_null($input1))
		{
			$this->connectInput($input1);
		}
		
		if (!is_null($input2))
		{ 
			$this->connectInput($input2);
		}
		
	}

	/** 
	 * Connect an input IPipeFitting.
	 * <P>
	 * NOTE: You can connect as many inputs as you want
	 * by calling this method repeatedly.</P>
	 * 
	 * @param input the IPipeFitting to connect for input.
	 * @return bool
	 */
	public function connectInput( IPipeFitting $input )
	{
		return $input->connect($this);
	}
	
}
