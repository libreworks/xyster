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
 * @package   Xyster_Filter
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';
/**
 * A filter for title case
 *
 * @category  Xyster
 * @package   Xyster_Filter
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Filter_TitleCase implements Zend_Filter_Interface
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
     * Converts a string to title case
     * 
     * @param string $value
     * @return string The input in title case
     */
    public function filter($value)
    {
        // Split the string into separate words 
        $words = explode(' ', strtolower($value));
        
        foreach ( $words as $key => $word ) {
            // If this is the first, or it's not a small word, capitalize it
            if ( $key == 0 or !in_array($word, self::$_smallWords) ) {
                $words[$key] = ucwords($word);
            }
        }
        
        return implode(' ', $words);
    }
}