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
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace Xyster\Collection;
/**
 * Simple Map Entry
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class MapEntry
{
    protected $_key;
    protected $_value;

    /**
     * Creates a new map entry
     *
     * @param mixed $key The key of the entry
     * @param mixed $value The value of the entry
     */
    public function __construct($key, $value)
    {
        $this->_key = $key;
        $this->_value = $value;
    }

    /**
     * Gets the key for this mapping
     *
     * @return mixed The key
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Gets the value for this mapping
     *
     * @return mixed The value
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets the value for this mapping
     *
     * @param mixed $value The new value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Gets the string equivalent of this mapping
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_key . "=" . $this->_value;
    }
}