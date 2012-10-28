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
require_once 'org/puremvc/php/multicore/interfaces/INotification.php';
require_once 'org/puremvc/php/multicore/patterns/mediator/Mediator.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeFitting.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/interfaces/IPipeMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/messages/FilterControlMessage.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/Junction.php';

/**
 * Junction Mediator.
 * <P>
 * A base class for handling the Pipe Junction in an IPipeAware 
 * Core.</P>
 * 
 * @package org.puremvc.php.multicore.utilities.pipes
 */
class JunctionMediator extends Mediator
{
	/**
	 * Accept input pipe notification name constant.
	 */ 
	const ACCEPT_INPUT_PIPE = 'acceptInputPipe';
		
	/**
	 * Accept output pipe notification name constant.
	 */ 
	const ACCEPT_OUTPUT_PIPE = 'acceptOutputPipe';

	/**
	 * Constructor.
	 * 
	 * @param string $name
	 * @param Junction $viewComponent
	 * @return JunctionMediator
	 */
	public function __construct( $name, Junction $viewComponent )
	{
		parent::__construct( $name, $viewComponent );
	}

	/**
	 * List Notification Interests.
	 * <P>
	 * Returns the notification interests for this base class.
	 * Override in subclass and call <code>super.listNotificationInterests</code>
	 * to get this list, then add any sublcass interests to 
	 * the array before returning.</P>
	 * 
	 * @return array
	 */
	public function listNotificationInterests()
	{
		return array( JunctionMediator::ACCEPT_INPUT_PIPE, 
		         	  JunctionMediator::ACCEPT_OUTPUT_PIPE
		       		);	
	}
	
	/**
	 * Handle Notification.
	 * <P>
	 * This provides the handling for common junction activities. It 
	 * accepts input and output pipes in response to <code>IPipeAware</code>
	 * interface calls.</P>
	 * <P>
	 * Override in subclass, and call <code>super.handleNotification</code>
	 * if none of the subclass-specific notification names are matched.</P>
	 * 
	 * @param INotification $notification
	 * @return void 
	 */
	public function handleNotification( INotification $notification )
	{
		switch( $notification->getName() )
		{
			// accept an input pipe
			// register the pipe and if successful 
			// set this mediator as its listener
			case JunctionMediator::ACCEPT_INPUT_PIPE:
				$inputPipeName = $notification->getType();
				$inputPipe = $notification->getBody();
				if ( $this->junction>registerPipe($inputPipeName, Junction::INPUT, $inputPipe) ) 
				{
					$this->junction->addPipeListener( $inputPipeName, $this, 'handlePipeMessage' );		
				} 
				break;
			
			// accept an output pipe
			case JunctionMediator::ACCEPT_OUTPUT_PIPE:
				$outputPipeName = $notification->getType();
				$outputPipe = $notification->getBody();
				$this->junction->registerPipe( $outputPipeName, Junction::OUTPUT, $outputPipe );
				break;
				
		}
	}
	
	/**
	 * Handle incoming pipe messages.
	 * <P>
	 * Override in subclass and handle messages appropriately for the module.</P>
	 * 
	 * @param IPipeMessage $message
	 * @return void
	 */
	public function handlePipeMessage( IPipeMessage $message )
	{
	}
	
	/**
	 * The Junction for this Module.
	 */
	/*
	protected function junction()
	{
		return $this->viewComponent;
	}
	*/
	
	protected function __get($mn)
	{
		if($mn == 'junction')
		{
			return $this->viewComponent;
		}
		else
		{
			return null;
		}
	}

}
