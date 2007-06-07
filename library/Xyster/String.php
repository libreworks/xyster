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
 * @package   Xyster
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Helper class for string manipulation 
 *
 * @category  Xyster
 * @package   Xyster
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_String
{
	/**
	 * An array of words that shouldn't be capitalized in title case
	 *
	 * @var array
	 */
	static private $_smallWords = array( 'of','a','the','and','an','or','nor',
		'but','is','if','then','else','when', 'at','from','by','on','off','for',
		'in','out','over','to','into','with' );

	private function __construct() {}

	/**
	 * Converts a string to title case
	 * 
	 * @return string The input in title case
	 */
	static function titleCase( $title )
	{	
		// Split the string into separate words 
		$words = explode(' ',$title);
		
		foreach ( $words as $key => $word ) {
			// If this is the first, or it's not a small word, capitalize it
			if ( $key == 0 or !in_array($word, self::$_smallWords) ) {
				$words[$key] = ucwords($word);
			}
		}
		
		return implode(' ', $words);
	}
	/**
	 * Converts a string in Underscore case to Camel case
	 *
	 * <code>
	 * $in = "this_is_underscore_case";
	 * $out = Xyster_String::toCamel($in);
	 * echo $out; // prints thisIsCamelCase
	 * </code>
	 *
	 * @param string $name  A string in Underscore case (this_is_underscore_case)
	 * @return string  The string in Camel case (thisIsCamelCase)
	 * @see toUnderscores()
	 */
	static public function toCamel( $name )
	{
		return preg_replace("/_([a-z])/e", "strtoupper('\\1')", $name);
	}
	/**
	 * Converts a string in Camel case to Underscore case
	 *
	 * <code>
	 * $in = "thisIsCamelCase";
	 * $out = Xyster_String::toUnderscores($in);
	 * echo $out; // prints this_is_underscore_case
	 * </code>
	 *
	 * @param string $name A string in Camel case (thisIsCamelCase)
	 * @return string The string in Underscore case (this_is_underscore_case)
	 * @see toCamel()
	 */
	static public function toUnderscores( $name )
	{
		return strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $name));
	}
}