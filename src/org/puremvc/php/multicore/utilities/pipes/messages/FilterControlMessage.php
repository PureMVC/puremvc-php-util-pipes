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
 * Filter Control Message.
 * <P>
 * A special message type for controlling the behavior of a Filter.</P>
 * <P> 
 * The <code>FilterControlMessage.SET_PARAMS</code> message type tells the Filter
 * to retrieve the filter parameters object.</P> 
 * 
 * <P> 
 * The <code>FilterControlMessage.SET_FILTER</code> message type tells the Filter
 * to retrieve the filter function.</P>
 * 
 * <P> 
 * The <code>FilterControlMessage.BYPASS</code> message type tells the Filter
 * that it should go into Bypass mode operation, passing all normal
 * messages through unfiltered.</P>
 * 
 * <P>
 * The <code>FilterControlMessage.FILTER</code> message type tells the Filter
 * that it should go into Filtering mode operation, filtering all
 * normal normal messages before writing out. This is the default
 * mode of operation and so this message type need only be sent to
 * cancel a previous  <code>FilterControlMessage.BYPASS</code> message.</P>
 * 
 * <P>
 * The Filter only acts on a control message if it is targeted 
 * to this named filter instance. Otherwise it writes the message
 * through to its output unchanged.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */ 
class FilterControlMessage extends Message
{
	/**
	 * @var object
	 */
	protected $params;
	
	/**
	 * @var function
	 */
	protected $filter;
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * Message type base URI
	 */
	const BASE = 'http://puremvc.org/namespaces/pipes/messages/filter-control/';
	
	/**
	 * Set filter parameters.
	 */ 
	const SET_PARAMS = 'http://puremvc.org/namespaces/pipes/messages/setparams';
	
	/**
	 * Set filter function.
	 */ 
	const SET_FILTER = 'http://puremvc.org/namespaces/pipes/messages/setfilter';

	/**
	 * Toggle to filter bypass mode.
	 */
	const BYPASS = 'http://puremvc.org/namespaces/pipes/messages/bypass';
	
	/**
	 * Toggle to filtering mode. (default behavior).
	 */
	const FILTER = 'http://puremvc.org/namespaces/pipes/messages/filter';


	/**
	 * Constructor
	 * 
	 * @param string $type
	 * @param string $name
	 * @param function $filter
	 * @param object $params
	 * @return FilterControlMessage
	 */
	public function __construct( $type, $name, $filter=null, $params=null )
	{
		parent::__construct( $type );
		$this->setName( $name );
		$this->setFilter( $filter );
		$this->setParams( $params );
	}

	/**
	 * Set the target filter name.
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setName( $name )
	{
		$this->name = $name;
	}
	
	/**
	 * Get the target filter name.
	 * 
	 * @return string
	 */
	public function getName( )
	{
		return $this->name;
	}
	
	/**
	 * Set the filter function.
	 * 
	 * @param function $filter
	 * @return void
	 */
	public function setFilter( $filter )
	{
		$this->filter = $filter;
	}
	
	/**
	 * Get the filter function.
	 * 
	 * @return function
	 */
	public function getFilter( )
	{
		return $this->filter;
	}
	
	/**
	 * Set the parameters object.
	 * 
	 * @param object $params
	 * @return void
	 */
	public function setParams( $params )
	{
		$this->params = $params;
	}
	
	/**
	 * Get the parameters object.
	 * 
	 * @return object
	 */
	public function getParams( )
	{
		return $this->params;
	}
	
}
