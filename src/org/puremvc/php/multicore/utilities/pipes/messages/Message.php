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
 * Pipe Message.
 * <P>
 * Messages travelling through a Pipeline can
 * be filtered, and queued. In a queue, they may
 * be sorted by priority. Based on type, they 
 * may used as control messages to modify the
 * behavior of filter or queue fittings connected
 * to the pipleline into which they are written.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */ 
class Message implements IPipeMessage
{

	/**
	 * High priority Messages can be sorted to the front of the queue
	 */
	const PRIORITY_HIGH = 1;
	
	/***
	 * Medium priority Messages are the default
	 */
	const PRIORITY_MED = 5;
	
	/**
	 * Low priority Messages can be sorted to the back of the queue
	 */ 
	const PRIORITY_LOW = 10;
	
	/**
	 * Normal Message type.
	 */
	const NORMAL = 'http://puremvc.org/namespaces/pipes/messages/normal/';
	
	/**
	 * TBD: Messages in a queue can be sorted by priority.
	 * @var int
	 */
	protected $priority;

	/**
	 * Messages can be handled differently according to type
	 * @var string
	 */
	protected $type;
	
	/**
	 * Header properties describe any meta data about the message for the recipient
	 * @var mixed
	 */
	protected $header;

	/**
	 * Body of the message is the precious cargo
	 * @var mixed
	 */
	protected $body;

	/**
	 * Constructor
	 * 
	 * @param string $type
	 * @param mixed $header
	 * @param mixed $body
	 * @param int $priority
	 * @return Message
	 */
	public function __construct( $type, $header=null, $body=null, $priority=5 )
	{
		$this->setType( $type );
		$this->setHeader( $header );
		$this->setBody( $body );
		$this->setPriority( $priority );
	}
	
	// 
	/**
	 * Get the type of this message
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
	
	// 
	/**
	 * Set the type of this message
	 * 
	 * @param string $type
	 * @return void
	 */
	public function setType( $type )
	{
		$this->type = $type;
	}
	
	// 
	/**
	 * Get the priority of this message
	 * 
	 * @return int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	// 
	/**
	 * Set the priority of this message
	 * 
	 * @param int $priority
	 * @return void
	 */
	public function setPriority( $priority )
	{
		$this->priority = $priority;
	}
	
	// 
	/**
	 * Get the header of this message
	 * 
	 * @return mixed
	 */
	public function getHeader()
	{
		return $this->header;
	}

	// 
	/**
	 * Set the header of this message
	 * 
	 * @param mixed $header
	 * @return void
	 */
	public function setHeader( $header )
	{
		$this->header = $header;
	}
	
	// 
	/**
	 * Get the body of this message
	 * 
	 * @return mixed
	 */
	public function getBody()
	{
		return $this->body;
	}

	// 
	/**
	 * Set the body of this message
	 * 
	 * @param mixed $body
	 * @return void
	 */
	public function setBody( $body )
	{
		$this->body = $body;
	}

}
