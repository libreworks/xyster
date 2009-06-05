<?php
/**
 * Xyster Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * @category  Xyster
 * @package   UnitTests
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

// Set error reporting to the level to which Xyster Framework code must comply
error_reporting( E_ALL | E_STRICT );

// Determine the root, library, and tests directories of the framework distribution
$xfRoot    = dirname(dirname(__FILE__));
$xfLibrary = $xfRoot . DIRECTORY_SEPARATOR . 'library';
$xfTests   = $xfRoot . DIRECTORY_SEPARATOR . 'tests';

/*
 * Omit from code coverage reports the contents of the tests directory
 */
PHPUnit_Util_Filter::addDirectoryToFilter($xfTests, ".php");

// Prepend the Xyster Framework library/ and tests/ directories to the 
// include_path. This allows the tests to run out of the box and helps prevent 
// loading other copies of the framework code and tests that would supersede 
// this copy.
$path = array(
    $xfLibrary,
    $xfTests,
    get_include_path()
    );
set_include_path(implode(PATH_SEPARATOR, $path));

// Load the user-defined test configuration file, if it exists;
// otherwise, load the default configuration.
if (is_readable($xfTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

// Add Xyster Framework library/ directory to the PHPUnit code coverage
// whitelist. This has the effect that only production code source files appear
// in the code coverage report and that all production code source files, even
// those that are not covered by a test yet, are processed.
if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === TRUE &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
    PHPUnit_Util_Filter::addDirectoryToWhitelist($xfLibrary);
}

// Unset global variables that are no longer needed
unset($xfRoot, $xfLibrary, $xfTests, $path);
