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

	/**
	 * Converts an associative array into a string representation
	 * 
	 * For instance, an example output of this method might look like:
	 * 
	 * <code>1=January,2=February,3=March</code>
	 * 
	 * @param array $array The array to stringify
	 * @return string The stringified array
	 */
	static function arrayToString( array $array )
	{
	    $string = array();
	    foreach( $array as $key => $value ) {
	        $string[] = $key . '=' . $value;
	    }
	    return implode(',', $string);
	}
	
	/**
	 * Like explode(), but won't split inside of parentheses or double-quotes
	 *
	 * This method will split a string into an array using a string as a
	 * seperator.  If the seperator is contained within a pair of double-quotes
	 * or between matching parentheses, it will be ignored.  If the seperator is
	 * not found, the method will return an array with one element containing
	 * the entire string.
	 *
	 * Example:
	 * <code>
	 * $haystack = 'A (great (test)) of "this method"';
	 * print_r( wfString::smartSplit(' ',$haystack) );
	 * </code>
	 *
	 * Would print:
	 * <pre>
	 * array (
	 *    [0]=>A
	 * 	  [1]=>(great (test))
	 * 	  [2]=>of
	 * 	  [3]=>"this method"
	 * )
	 * </pre>
	 *
	 * @param string $needle  String used as seperator
	 * @param string $haystack  String to split
	 * @param bool $caseInsensitive  Whether to ignore case
	 * @return array  Split parts of $haystack
	 */
	static public function smartSplit( $needle, $haystack, $caseInsensitive=true )
	{
		$split = array();
		$buff = "";
		$inPar = 0;
		$inStr = false;
		for( $i=0; $i<strlen($haystack); $i++ ) {
			$currNeed = substr( $haystack, $i, strlen($needle) );
			$curr = $currNeed{0};
			$last = ( $i ) ? $haystack{$i-1} : "";
			if ( $curr == '"' && ( ( $inStr && $last != "\\") || !$inStr ) ) {
				$inStr = !$inStr;
			}
			if ( !$inStr ) {
				if ( $curr == "(" ) {
					$inPar++;
				} else if ( $curr == ")" ) {
					$inPar--;
				}
			}
			if ( !$inPar && !$inStr &&
				( $currNeed == $needle ||
				( $caseInsensitive && strcasecmp($currNeed,$needle) == 0 ) ) ) {
				$split[] = $buff;
				$buff = "";
				$i = $i+(strlen($needle)-1);
			} else {
				$buff .= $curr;
			}
		}
		$split[] = $buff;
		return $split;
	}
	
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