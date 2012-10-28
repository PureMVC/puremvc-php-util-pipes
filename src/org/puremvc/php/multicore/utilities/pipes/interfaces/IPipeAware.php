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

/**
 * Pipe Aware interface.
 * 
 * Can be implemented by any PureMVC Core that wishes
 * to communicate with other Cores using the Pipes 
 * utility.
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
interface IPipeAware
{
	/**
	 * @param string $name
	 * @param IPipeFitting $pipe
	 * @return void
	 */
	function acceptInputPipe( $name, IPipeFitting $pipe );

	/**
	 * @param string $name
	 * @param IPipeFitting $pipe
	 * @return void
	 */
	function acceptOutputPipe( $name, IPipeFitting $pipe );
}
