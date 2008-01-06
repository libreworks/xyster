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
 * @package   Xyster
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Helper class for string manipulation 
 *
 * @category  Xyster
 * @package   Xyster
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_String
{
    /**
     * Used for matching parenthesis groups not inside quotes
     * 
     * It works, but this still matches parentheses inside quotes that aren't in
     * parentheses.
     */
    const PARENTH_QUOTE_REGEX = '/(?<![\w\d])(\(((?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|[^()])|(?1))+\))/m';

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
	static public function arrayToString( array $array )
	{
	    $string = array();
	    foreach( $array as $key => $value ) {
	        $string[] = $key . '=' . $value;
	    }
	    return implode(',', $string);
	}
	
    /**
     * Match nested parentheses groups not inside of double-quotes
     * 
     * This method will find all balanced parentheses not inside of double
     * quotes.  It also respects escaped double quotes (i.e. with a preceding
     * backslash).
     * 
     * For example, if you ran this method on a string containing JavaScript
     * code: 
     * <code>
     * $string = <<<HEREDOC
     * var n = navigator;
     * return ( n.appName == "Netscape" ) || n.appVersion != "5.0 (Windows; en-US)";
     * HEREDOC;
     * </code> 
     * 
     * You would receive back an array containing:
     * <pre>
     * array (
     *     [0]=>( n.appName == "Netscape" )
     * );
     * </pre>
     * 
     * The second matched parentheses are inside of quotation marks and thus
     * should be treated as part of that string literal.
     *
     * @param string $string
     * @return array
     * @todo make this work with top-level strings with parenths in them
     */
    static public function matchGroups( $string )
    {
        if ( strpos($string, '(') === false ) {
            return array();
        }
        
        $matches = array();
        preg_match_all(self::PARENTH_QUOTE_REGEX, $string, $matches, PREG_SET_ORDER);
        
        $groups = array();
        foreach( $matches as $group ) {
            $groups[] = $group[0];
        }
        return $groups;
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
	 * print_r(Xyster_String::smartSplit(' ', $haystack));
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
			$curr = $currNeed[0];
			$last = ( $i ) ? $haystack[$i-1] : "";
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