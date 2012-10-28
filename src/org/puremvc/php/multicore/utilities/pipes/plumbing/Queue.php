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
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/Message.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/QueueControlMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Pipe.php';

/** 
 * Pipe Queue.
 * <P>
 * The Queue always stores inbound messages until you send it
 * a FLUSH control message, at which point it writes its buffer 
 * to the output pipe fitting. The Queue can be sent a SORT 
 * control message to go into sort-by-priority mode or a FIFO 
 * control message to cancel sort mode and return the
 * default mode of operation, FIFO.</P>
 * 
 * <P>
 * NOTE: There can effectively be only one Queue on a given 
 * pipeline, since the first Queue acts on any queue control 
 * message. Multiple queues in one pipeline are of dubious 
 * use, and so having to name them would make their operation 
 * more complex than need be.</P> 
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
class Queue extends Pipe
{
	/**
	 * @var string
	 */
	protected $mode = QueueControlMessage::FIFO;
	
	/**
	 * 
	 * @var array
	 */
	protected $messages = array();
	
	public function Queue( IPipeFitting $output=null )
	{
		parent::__construct( $output );
	}
	
	/**
	 * Handle the incoming message.
	 * <P>
	 * Normal messages are enqueued.</P>
	 * <P>
	 * The FLUSH message type tells the Queue to write all 
	 * stored messages to the ouptut PipeFitting, then 
	 * return to normal enqueing operation.</P>
	 * <P>
	 * The SORT message type tells the Queue to sort all 
	 * <I>subsequent</I> incoming messages by priority. If there
	 * are unflushed messages in the queue, they will not be
	 * sorted unless a new message is sent before the next FLUSH.
	 * Sorting-by-priority behavior continues even after a FLUSH, 
	 * and can be turned off by sending a FIFO message, which is 
	 * the default behavior for enqueue/dequeue.</P>
	 *  
 	 * @param IPipeMessage $message
 	 * @return bool
 	 */
	public function write( IPipeMessage $message )
	{
		$success = true;
		switch ( $message->getType() )	
		{
			// Store normal messages
			case Message::NORMAL:
				$this->store( $message );
				break;
				
			// Flush the queue
			case QueueControlMessage::FLUSH:
				$success = $this->flush();		
				break;

			// Put Queue into Priority Sort or FIFO mode 
			// Subsequent messages written to the queue
			// will be affected. Sorted messages cannot
			// be put back into FIFO order!
			case QueueControlMessage::SORT:
			case QueueControlMessage::FIFO:
				$this->mode = $message->getType();
				break;
		}
		return $success;
	} 
	
	/**
	 * Store a message.
	 * @param message the IPipeMessage to enqueue.
	 * @return int the new count of messages in the queue
	 */
	protected function store( IPipeMessage $message ) 
	{
		array_push($this->messages, $message );
		
		if ($this->mode == QueueControlMessage::SORT )
		{
			usort($this->messages,array($this, "sortMessagesByPriority"));
		}
	}


	/**
	 * Sort the Messages by priority.
	 */
	protected function sortMessagesByPriority(IPipeMessage $msgA, IPipeMessage $msgB)
	{
	    if ($msgA->getPriority() == $msgB->getPriority()) 
	    {
	        return 0;
	    }
	    return ($msgA->getPriority() < $msgB->getPriority()) ? -1 : 1;
	}
	
	/**
	 * Flush the queue.
	 * <P>
	 * NOTE: This empties the queue.</P>
	 * @return bool Boolean true if all messages written successfully.
	 */
	protected function flush()
	{
		$success = true;
		$message = array_shift($this->messages);
		while ( $message != null ) 
		{
			$ok = $this->output->write( $message );
			if ( !$ok )
			{
				$success = false;
			}
			$message = array_shift($this->messages);
		} 
		return $success;
	}

}
