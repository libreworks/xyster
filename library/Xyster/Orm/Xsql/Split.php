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
 * A string exploder that won't split inside paretheses or double-quotes
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Xsql_Split
{
	/**
	 * @var string
	 */
	protected $_needle;
	
	/**
	 * @var int
	 */
	protected $_needleLength;
	
	/**
	 * @var array
	 */
	protected static $_splits = array();
    	
	/**
	 * Creates a new splitter
	 *
	 * @param string $needle
	 */
	protected function __construct( $needle )
	{
		$this->_needle = $needle;
		$this->_needleLength = strlen($needle);
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
     * $split = Xyster_Orm_Xsql_Split::Space();
     * print_r($split->split(' ', $haystack));
     * </code>
     *
     * Would print:
     * <pre>
     * array (
     *    [0]=>A
     *    [1]=>(great (test))
     *    [2]=>of
     *    [3]=>"this method"
     * )
     * </pre>
     *
     * @param string $haystack  String to split
     * @param boolean $caseInsensitive  Whether to ignore case
     * @return array  Split parts of $haystack
     */
    public function split( $haystack, $caseInsensitive = true )
    {
    	$needle = $this->_needle;
    	$nlength = $this->_needleLength;
    	
        $split = array();
        $buff = "";
        $inPar = 0;
        $inStr = false;
        
        for( $i=0; $i<strlen($haystack); $i++ ) {
            $currNeed = substr($haystack, $i, $nlength);
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
                ( $caseInsensitive && strcasecmp($currNeed, $needle) == 0 ) ) ) {
                $split[] = $buff;
                $buff = "";
                $i = $i+($nlength-1);
            } else {
                $buff .= $curr;
            }
        }
        $split[] = $buff;
        
        return $split;
    }
    
    /**
     * Gets a splitter for the arrow ('->')
     *
     * @return Xyster_Orm_Xsql_Split
     */
    public static function Arrow()
    {
    	return self::_factory('->');
    }
    
    /**
     * Gets a splitter for a comma (',')
     *
     * @return Xyster_Orm_Xsql_Split
     */
    public static function Comma()
    {
        return self::_factory(',');
    }
    
    /**
     * Gets a splitter for any string
     *
     * @return Xyster_Orm_Xsql_Split
     */
    public static function Custom( $needle )
    {
        return self::_factory($needle);
    }
    
    /**
     * Gets a splitter for a space (' ')
     *
     * @return Xyster_Orm_Xsql_Split
     */
    public static function Space()
    {
        return self::_factory(' ');
    }
        
    /**
     * Creates a new splitter
     *
     * @param string $needle
     * @return Xyster_Orm_Xsql_Split
     */
    protected static function _factory( $needle )
    {
    	if ( !isset(self::$_splits[$needle]) ) {
    		self::$_splits[$needle] = new self($needle);
    	}
    	return self::$_splits[$needle];
    }
}