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
 * @package   Xyster_Date
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Date;
/**
 * A date range
 *
 * @category  Xyster
 * @package   Xyster_Date
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Range
{
    /**
     * @var \Zend_Date
     */
    protected $_start;
    
    /**
     * @var \Zend_Date
     */
    protected $_end;
    
    /**
     * A date range
     *
     * @param \Zend_Date $start The starting date
     * @param \Zend_Date $end The ending date
     * @throws \Zend_Date_Exception if the end date occurs before the start
     */
    public function __construct( \Zend_Date $start, \Zend_Date $end )
    {
        if ( $end->isEarlier($start) ) {
            require_once 'Zend/Date/Exception.php';
            throw new \Zend_Date_Exception('The end date occurs before the start date');
        }
        $this->_start = $start;
        $this->_end = $end;
    }
    
    /**
     * Gets the ending date
     *
     * @return Zend_Date the ending date
     */
    public function getEnd()
    {
        return clone $this->_end;
    }
    
    /**
     * Gets the beginning date
     *
     * @return Zend_Date the beginning date
     */
    public function getStart()
    {
        return clone $this->_start;
    }
    
    /**
     * Gets the timespan between these dates
     * 
     * The part and locale are the same as used in many Zend_Date methods.
     *
     * @param  string $part OPTIONAL Part of the date to sub, if null the timestamp is subtracted
     * @param  mixed $locale OPTIONAL Locale as a Zend_Locale or a string for parsing input
     * @return mixed the timespan
     */
    public function getTimespan($part = \Zend_Date::TIMESTAMP, $locale = null)
    {
        return $this->getEnd()->sub($this->_start, $part, $locale);
    }
    
    /**
     * Tests to see if the supplied date is within this range
     *
     * The part and locale are the same as used in many Zend_Date methods.
     * 
     * @param mixed $date Date or datepart to compare with the date object
     * @param string $part OPTIONAL Part of the date to compare, if null the timestamp is compared
     * @param mixed $locale OPTIONAL Locale as a \Zend_Locale or a string for parsing input
     * @return boolean 
     * @throws \Zend_Date_Exception
     */
    public function isWithin( $date, $part = \Zend_Date::TIMESTAMP, $locale = null )
    {
        return ($this->_start->isEarlier($date, $part, $locale) ||
            $this->_start->equals($date, $part, $locale)) &&
            ($this->_end->isLater($date, $part, $locale) ||
            $this->_end->equals($date, $part, $locale));
    }
    
    /**
     * Returns the string representation of this object
     *
     * The dates are separated by an En Dash (U+2013)
     * 
     * @magic
     * @return string
     */
    public function __toString()
    {
        return $this->_start . '–' . $this->_end;
    }
}