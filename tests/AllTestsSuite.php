<?php
/**
 * PureMVC PHP MultiCore Utility - Pipes Unit Tests
 * 
 * A PHP port of Cliff Hall
 * PureMVC AS3/MultiCore Utility - Pipes 1.1
 * 
 * Created on Jully 29, 2009
 * 
 * @version 1.0
 * @author Michel Chouinard <michel.chouinard@gmail.com>
 * @copyright PureMVC - Copyright(c) 2006-2008 Futurescale, Inc., Some rights reserved.
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
/**
 * 
 */

// Define path to PureMVC PHP Multicore directory
// Replace this with the absolute root location of your copy of 
// PureMVC PHP Multicore Library or copy the library into the root
// of this project and set this constant to ''
defined('PUREMVC_PHP_MULTICORE_LIBRARY_PATH')
    || define('PUREMVC_PHP_MULTICORE_LIBRARY_PATH', 'D:/eclipse/Workspaces/PureMVC_PHP_MultiCore_1_0_0;');

// Define path to PureMVC PHP MultiCore Utilities Pipes directory
// Replace this with the absolute root location of your copy of 
// PureMVC PHP MultiCore Utilities Pipes or copy the library into the root
// of this project and set this constant to ''
defined('PUREMVC_PHP_MULTICORE_UTILITY_PIPES_LIBRARY_PATH')
    || define('PUREMVC_PHP_MULTICORE_UTILITY_PIPES_LIBRARY_PATH', 'D:\eclipse\Workspaces\PureMVC_PHP_MultiCore_Utility_Pipes_1_0_0;');

    
set_include_path( PUREMVC_PHP_MULTICORE_LIBRARY_PATH.
				  PUREMVC_PHP_MULTICORE_UTILITY_PIPES_LIBRARY_PATH.
				  get_include_path()
				);

require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/FilterTest.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/JunctionTest.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/MessageTest.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/PipeListenerTest.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/PipeTest.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/QueueTest.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/TeeMergeTest.php';
require_once 'org/puremvc/php/multicore/utilities/pipes/plumbing/TeeSplitTest.php';

/**
 * Static test suite.
 * @package org.puremvc.php.multicore.utilities.pipes.unittest
 */
class AllTestsSuite extends PHPUnit_Framework_TestSuite
{
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName( 'AllTestsSuite' );
		$this->addTestSuite( 'FilterTest' );
		$this->addTestSuite( 'JunctionTest' );
		$this->addTestSuite( 'MessageTest' );
		$this->addTestSuite( 'PipeListenerTest' );
		$this->addTestSuite( 'PipeTest' );
		$this->addTestSuite( 'QueueTest' );
		$this->addTestSuite( 'TeeMergeTest' );
		$this->addTestSuite( 'TeeSplitTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		return new self();
	}
}

