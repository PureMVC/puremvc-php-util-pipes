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
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/FilterControlMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/Message.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Pipe.php';

/**
 * Pipe Filter.
 * <P>
 * Filters may modify the contents of messages before writing them to 
 * their output pipe fitting. They may also have their parameters and
 * filter function passed to them by control message, as well as having
 * their Bypass/Filter operation mode toggled via control message.</p>  
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */ 
class Filter extends Pipe
{
	/**
	 * @var string 
	 */
	protected $mode = FilterControlMessage::FILTER;

	/**
	 * @var function 
	 */
	protected $filter = null; //default to function($message, $params){return;};
	
	/**
	 * @var object 
	 */
	protected $params = null;
	
	/**
	 * @var string 
	 */
	protected $name;
	
	/**
	 * Constructor.
	 * <P>
	 * Optionally connect the output and set the parameters.</P>
	 * 
	 * @param string $name
	 * @param IPipeFitting $output
	 * @param function $filter
	 * @param object $params
	 * @return Filter
	 */
	public function Filter( $name, IPipeFitting $output=null, $filter=null, $params=null ) 
	{
		parent::__construct( $output );
		$this->name = $name;
		$this->setFilter( $filter );
		$this->setParams( $params );
	}

	/**
	 * Handle the incoming message.
	 * <P>
	 * If message type is normal, filter the message (unless in BYPASS mode)
	 * and write the result to the output pipe fitting if the filter 
	 * operation is successful.</P>
	 * 
	 * <P> 
	 * The FilterControlMessage.SET_PARAMS message type tells the Filter
	 * that the message class is FilterControlMessage, which it 
	 * casts the message to in order to retrieve the filter parameters
	 * object if the message is addressed to this filter.</P> 
	 * 
	 * <P> 
	 * The FilterControlMessage.SET_FILTER message type tells the Filter
	 * that the message class is FilterControlMessage, which it 
	 * casts the message to in order to retrieve the filter function.</P>
	 * 
	 * <P> 
	 * The FilterControlMessage.BYPASS message type tells the Filter
	 * that it should go into Bypass mode operation, passing all normal
	 * messages through unfiltered.</P>
	 * 
	 * <P>
	 * The FilterControlMessage.FILTER message type tells the Filter
	 * that it should go into Filtering mode operation, filtering all
	 * normal normal messages before writing out. This is the default
	 * mode of operation and so this message type need only be sent to
	 * cancel a previous BYPASS message.</P>
	 * 
	 * <P>
	 * The Filter only acts on the control message if it is targeted 
	 * to this named filter instance. Otherwise it writes through to the
	 * output.</P>
	 * 
	 * @param IPipeMessage $message
	 * @return Boolean True if the filter process does not throw an error and subsequent operations 
	 * in the pipeline succede.
	 */
	public function write( IPipeMessage $message )
	{
		$outputMessage = null;
		$success = true;

		// Filter normal messages
		switch ( $message->getType())
		{
			case  Message::NORMAL: 	
				try 
				{
					if ( $this->mode == FilterControlMessage::FILTER ) 
					{
						$outputMessage = $this->applyFilter( $message );
					} 
					else 
					{
						$outputMessage = $message;
					}
					$success = $this->output->write( $outputMessage );
				} 
				catch (Exception $e) 
				{
					$success = false;
				}
				break;
			
			// Accept parameters from control message 
			case FilterControlMessage::SET_PARAMS:
				if ($this->isTarget($message)) 					
				{
					$this->setParams( $message->getParams() );
				} 
				else 
				{
					$success = $this->output->write( $outputMessage );
				}
				break;

			// Accept filter function from control message 
			case FilterControlMessage::SET_FILTER:
				if ($this->isTarget($message))
				{
					$this->setFilter( $message->getFilter() );
				} 
				else 
				{
					$success = $this->output->write( $outputMessage );
				}
				
				break;

			// Toggle between Filter or Bypass operational modes
			case FilterControlMessage::BYPASS:
			case FilterControlMessage::FILTER:
				if ($this->isTarget($message))
				{
					$this->mode = $message->getType();
				} 
				else 
				{
					$success = $this->output->write( $outputMessage );
				}
				break;
			
			// Write control messages for other fittings through
			default:
				$success = $this->output->write( $outputMessage );
		}
		return $success;			
	}
	
	/**
	 * Is the message directed at this filter instance?
	 * 
	 * @param IPipeMessage $message
	 * @return bool
	 */
	protected function isTarget($message)
	{
		return ( $message->getName() == $this->name );
	}
	
	/**
	 * Set the Filter parameters.
	 * <P>
	 * This can be an object can contain whatever arbitrary 
	 * properties and values your filter method requires to
	 * operate.</P>
	 * 
	 * @param object $params the parameters object
	 */
	public function setParams( $params )
	{
		$this->params = $params;
	}

	/**
	 * Set the Filter function.
	 * <P>
	 * It must accept two arguments; an IPipeMessage, 
	 * and a parameter Object, which can contain whatever 
	 * arbitrary properties and values your filter method 
	 * requires.</P>
	 * 
	 * @param function $filter the filter function.
	 * @return void 
	 */
	public function setFilter( $filter )
	{
		$this->filter = (is_null($filter) ? (version_compare(phpversion(), '5.3.0', '<') ? create_function('$message, $params','return;') : function($message, $params){return;}) : $filter);
	}
	
	/**
	 * Filter the message.
	 * 
	 * @param IPipeMessage $message
	 * @return IPipeMessage
	 */
	protected function applyFilter( IPipeMessage $message )
	{
		$filter = $this->filter;
		
		$filter($message, $this->params);
		return $message;
	}
	
}
