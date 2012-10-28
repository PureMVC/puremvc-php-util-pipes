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
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeMessage.php';

/** 
 * Pipe Fitting Interface.
 * <P>
 * An <code>IPipeFitting</code> can be connected to other 
 * <code>IPipeFitting</code>s, forming a Pipeline. 
 * <code>IPipeMessage</code>s are written to one end of a 
 * Pipeline by some client code. The messages are then 
 * transfered in synchronous fashion from one fitting to 
 * the next.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
interface IPipeFitting
{
	/**
	 * Connect another Pipe Fitting to the output.
	 * <P>
	 * Fittings connect and write to 
	 * other fittings in a one way syncrhonous
	 * chain, as water typically flows one direction 
	 * through a physical pipe.</P>
	 * 
	 * @param IPipeFitting $output
	 * @return bool Boolean true if no other fitting was already connected.
	 */
	function connect( IPipeFitting $output );

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
	function disconnect( );

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
	function write( IPipeMessage $message );

			
}
