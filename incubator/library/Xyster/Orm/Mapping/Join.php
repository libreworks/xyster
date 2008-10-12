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
 * A join representation
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapping_Join
{
    protected $_entity;
    
    /**
     * Sets the mapped class 
     * 
     * @param Xyster_Orm_Mapping_Class_Abstract $class
     * @return Xyster_Orm_Mapping_Join provides a fluent interface
     */
    public function setMappedClass( Xyster_Orm_Mapping_Class_Abstract $class )
    {
        $this->_entity = $class;
        return $this;
    }
}