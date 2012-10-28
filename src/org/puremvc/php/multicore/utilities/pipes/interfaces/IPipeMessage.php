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
 * Pipe Message Interface.
 * <P>
 * <code>IPipeMessage</code>s are objects written intoto a Pipeline, 
 * composed of <code>IPipeFitting</code>s. The message is passed from 
 * one fitting to the next in syncrhonous fashion.</P> 
 * <P>
 * Depending on type, messages may be handled  differently by the 
 * fittings.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
interface IPipeMessage
{
	/**
	 * Get the type of this message
	 * @return string
	 */
	function getType();

	/**
	 * Set the type of this message
	 * @param string $type
	 * @return void
	 */
	function setType($type);
	
	/**
	 * Get the priority of this message
	 * @return int
	 */
	function getPriority();

	/**
	 * Set the priority of this message
	 * @param int $priority
	 * @return void
	 */
	function setPriority($priority);
	
	/**
	 * Get the header of this message
	 * @return mixed
	 */
	function getHeader();

	/**
	 * Set the header of this message
	 * @param mixed $header
	 * @return void
	 */
	function setHeader($header);
	
	/**
	 * Get the body of this message
	 * @return mixed
	 */
	function getBody();

	/**
	 * Set the body of this message
	 * @param mixed $body
	 * @return void
	 */
	function setBody($body);
}
