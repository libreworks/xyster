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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Xsql_Split
 */
require_once 'Xyster/Orm/Xsql/Split.php';
/**
 * A helper for XSQL syntax
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Xsql
{
    /**
     * Used for matching parenthesis groups not inside quotes
     * 
     * It works, but this still matches parentheses inside quotes that aren't in
     * parentheses.
     */
    const PARENTH_QUOTE_REGEX = '/(?<![\w\d])(\(((?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|[^()])|(?1))+\))/m';
    
    /**
     * Checks if a literal is a method call
     *
     * @param string $field
     * @return boolean
     */
    public static function isMethodCall( $field, array &$matches = array() )
    {
        return (bool) preg_match("/^[a-z_][a-z0-9_]*\([\w\W]*\)$/i", $field, $matches);
    }

    /**
     * Checks a literal for syntactical correctness
     *
     * @param string $lit
     * @return boolean
     */
    public static function isLiteral( $lit )
    {
        return (
            // either a string or a number or the word "null"
            preg_match("/^(\"[^\"]*\"|[\d]+(.[\d]+)?|null)$/i", trim($lit))
            //  a string with escapes in it
            || preg_match('/^"[^"\\\\]*(\\\\.[^"\\\\]*)*"$/', trim($lit))
            // check to see if it's a field
            || self::isValidField($lit)
        );
    }
        
    /**
     * Checks a reference for syntactical correctness
     *
     * @param string $field
     * @return boolean
     */
    public static function isValidField( $field )
    {
        $field = trim($field);

        $ok = true;
        if ( !preg_match("/^[a-z][a-z0-9_]*(->[a-z0-9_]+(\([\s]*\))?)*$/i", $field) ) {
        	$mcs = Xyster_Orm_Xsql_Split::Arrow()->split($field);
            foreach( $mcs as $mc ) {
                $matches = array();
                $match = preg_match( "/^[a-z][a-z0-9_]*(\((?P<params>[\w\W]*)\))?$/i", $mc, $matches );
                if ( ( $match && array_key_exists("params", $matches) && strlen(trim($matches['params']))
                && !self::_checkMethodParameters($matches['params']) ) || !$match ) {
                    $ok = false;
                    break;
                }
            }
        }
        return $ok;
    }
    
    /**
     * Match nested parentheses groups not inside of double-quotes
     * 
     * This method will find all balanced parentheses not inside of double
     * quotes.  It also respects escaped double quotes (i.e. with a preceding
     * backslash).
     *
     * @param string $string
     * @return array
     * @todo make this work with top-level strings with parenths in them
     */
    public static function matchGroups( $string )
    {
    	$groups = array();
    	
        if ( strpos($string, '(') !== false ) {
	        $matches = array();
	        preg_match_all(self::PARENTH_QUOTE_REGEX, $string, $matches, PREG_SET_ORDER);
	        foreach( $matches as $group ) {
	            $groups[] = $group[0];
	        }
        }
        
        return $groups;
    }

    /**
     * Splits a string on the arrow ('->')
     *
     * @param string $haystack
     * @return array
     */
    public static function splitArrow( $haystack )
    {
    	return Xyster_Orm_Xsql_Split::Arrow()->split($haystack);
    }
    
    /**
     * Splits a string on the comma (',')
     *
     * @param string $haystack
     * @return array
     */
    public static function splitComma( $haystack )
    {
        return Xyster_Orm_Xsql_Split::Comma()->split($haystack);
    }
    
    /**
     * Splits a string on the space (' ')
     *
     * @param string $haystack
     * @return array
     */
    public static function splitSpace( $haystack )
    {
        return Xyster_Orm_Xsql_Split::Space()->split($haystack);
    }
        
    /**
     * Checks method parameters for syntactical correctness
     *
     * @param array $params
     * @return boolean
     */
    protected static function _checkMethodParameters( $params )
    {
        $ok = true;
        foreach( Xyster_Orm_Xsql_Split::Comma()->split(trim($params)) as $p ) {
            if ( !self::isLiteral($p) ) {
                $ok = false;
                break;
            }
        }
        return $ok;
    }
}