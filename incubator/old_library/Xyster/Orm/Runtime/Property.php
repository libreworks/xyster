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
 * A runtime representation of a Property
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Runtime_Property
{
    /**
     * @var string
     */
    protected $_name;
    /**
     * @var Xyster_Orm_Type_Interface
     */
    protected $_type;
    
    /**
     * Creates a new property
     *
     * @param string $name
     * @param Xyster_Orm_Type_Interface $type
     */
    public function __construct( $name, Xyster_Orm_Type_Interface $type )
    {
        $this->_name = $name;
        $this->_type = $type;
    }
    
    /**
     * Gets the name of the property
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the type of the property
     *
     * @return Xyster_Orm_Type_Interface
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return 'Property(' . $this->_name . ':' . $this->_type->getName() . ')';
    }
}