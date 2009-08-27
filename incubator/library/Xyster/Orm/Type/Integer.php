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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: Integer.php 409 2009-08-23 21:59:38Z jonathanhawk $
 */
/**
 * @see Xyster_Orm_Type_IVersion
 */
require_once 'Xyster/Orm/Type/IVersion.php';
/**
 * @see Xyster_Orm_Type_Immutable
 */
require_once 'Xyster/Orm/Type/Immutable.php';
/**
 * The int type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Integer extends Xyster_Orm_Type_Immutable implements Xyster_Orm_Type_IVersion 
{
    /**
     * Compare two instances of this type
     *
     * @param mixed $a
     * @param mixed $b
     * @return int -1, 0, or 1
     */
    public function compare( $a, $b )
    {
        if ( $a > $b ) {
            return 1;
        } else if ( $a == $b ) {
            return 0;
        } else {
            return -1;
        }
    }
    
    /**
     * Gets a comparator for version values
     *
     * @return Xyster_Collection_Comparator_Interface
     */
    public function getComparator()
    {
        return $this;
    }
    
    /**
     * Gets the fetch type for binding
     *
     * @return int
     */
    public function getFetchType()
    {
        return Zend_Db::PARAM_INT;
    }
        
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Integer();
    }
    
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'integer';
    }
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnedType()
    {
        return Xyster_Type::integer();
    }

    /**
     * Gets the initial version id
     *
     * @return mixed The initial version
     */
    public function initial()
    {
        return 0;
    }
        
    /**
     * Gets the next version id
     *
     * @param mixed $current
     * @return mixed The next version
     */
    public function next( $current )
    {
        return $current + 1;
    }  
}