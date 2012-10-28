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
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/Message.php';

/**
 * Queue Control Message.
 * <P>
 * A special message for controlling the behavior of a Queue.</P>
 * <P>
 * When written to a pipeline containing a Queue, the type
 * of the message is interpreted and acted upon by the Queue.</P>
 * <P>
 * Unlike filters, multiple serially connected queues aren't 
 * very useful and so they do not require a name. If multiple
 * queues are connected serially, the message will be acted 
 * upon by the first queue only.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */ 
class QueueControlMessage extends Message
{
	/**
	 * Flush the queue.
	 */
	const FLUSH = 'http://puremvc.org/namespaces/pipes/messages/queue/flush';
	
	/**
	 * Toggle to sort-by-priority operation mode.
	 */
	const SORT = 'http://puremvc.org/namespaces/pipes/messages/queue/sort';
	
	/**
	 * Toggle to FIFO operation mode (default behavior).
	 */
	const FIFO = 'http://puremvc.org/namespaces/pipes/messages/queue/fifo';
	
	/**
	 * Constructor
	 * 
	 * @param string $type
	 * @return QueueControlMessage
	 */
	public function __construct( $type )
	{
		parent::__construct( $type  );
	}

}
