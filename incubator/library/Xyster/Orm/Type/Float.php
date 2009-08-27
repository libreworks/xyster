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
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Type_Immutable
 */
require_once 'Xyster/Orm/Type/Immutable.php';
/**
 * Maps a SQL DOUBLE PRECISION (8-bytes) to a PHP float
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Float extends Xyster_Orm_Type_Immutable
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
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Float();
    }
        
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'float';
    }
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnedType()
    {
        return Xyster_Type::double();
    }
}