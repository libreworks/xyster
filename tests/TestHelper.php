<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   UnitTests
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

// Set error reporting to the level to which Xyster Framework code must comply
error_reporting( E_ALL | E_STRICT );

// Determine the root, library, and tests directories of the framework distribution
$xfRoot    = dirname(dirname(__FILE__));
$xfLibrary = $xfRoot . DIRECTORY_SEPARATOR . 'library';
$xfTests   = $xfRoot . DIRECTORY_SEPARATOR . 'tests';

/*
Prepend the Xyster Framework library/ and tests/ directories to the include_path. This allows the tests to run out of
the box and helps prevent loading other copies of the framework code and tests that would supersede this copy.
*/
set_include_path($xfLibrary . PATH_SEPARATOR
               . $xfTests   . PATH_SEPARATOR
               . get_include_path());

// Load the user-defined test configuration file, if it exists; otherwise, load the default configuration
if (is_readable($xfTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

// Unset global variables no longer needed
unset($xfRoot, $xfLibrary, $xfTests);
