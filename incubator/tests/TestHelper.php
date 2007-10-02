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

require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/Util/Filter.php';

// Set error reporting to the level to which Xyster Framework code must comply
error_reporting( E_ALL | E_STRICT );

/*
 * Determine the root, library, and tests directories of the framework 
 * distribution.
 */
$xfRoot        = dirname(dirname(dirname(__FILE__)));
$xfIncLibrary  = $xfRoot . DIRECTORY_SEPARATOR . 'incubator' . DIRECTORY_SEPARATOR . 'library';
$xfIncTests    = $xfRoot . DIRECTORY_SEPARATOR . 'incubator' . DIRECTORY_SEPARATOR . 'tests';
$xfCoreLibrary = $xfRoot . DIRECTORY_SEPARATOR . 'library';
$xfCoreTests   = $xfRoot . DIRECTORY_SEPARATOR . 'tests';

/*
 * Prepend the Xyster Framework library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the framework code and tests that would supersede
 * this copy.
 */
$path = array();
$path[] = $xfIncTests;
if (isset($xfUseCoreTests) && $xfUseCoreTests === true) {
    $path[] = $xfCoreTests;
}
$path[] = $xfIncLibrary;
$path[] = $xfCoreLibrary;
$path[] = get_include_path();
set_include_path(implode(PATH_SEPARATOR, $path));


// Load the user-defined test configuration file, if it exists;
// otherwise, load the default configuration.
if (is_readable($xfIncTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

// Add Xystr Framework library/ directory to the PHPUnit code coverage
// whitelist. This has the effect that only production code source files appear
// in the code coverage report and that all production code source files, even
// those that are not covered by a test yet, are processed.
if (TESTS_GENERATE_REPORT === TRUE &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
    PHPUnit_Util_Filter::addDirectoryToWhitelist($xfCoreLibrary);
    PHPUnit_Util_Filter::addDirectoryToWhitelist($xfIncLibrary);
}

// Unset global variables that are no longer needed
unset($xfRoot, $xfCoreLibrary, $xfCoreTests);
unset($xfIncLibrary, $xfIncTests, $xfUseCoreTests);
