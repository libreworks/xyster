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
 * The boolean type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Type_Boolean extends Xyster_Orm_Type_Immutable
{
    /**
     * Gets the fetch type for binding
     *
     * @return int
     */
    public function getFetchType()
    {
        return Zend_Db::PARAM_BOOL;
    }
        
    /**
     * Gets the underlying database type
     *
     * @return Xyster_Db_DataType
     */
    public function getDataType()
    {
        return Xyster_Db_DataType::Boolean();
    }
    
    /**
     * Returns the type name
     *
     * @return string
     */
    public function getName()
    {
        return 'boolean';
    }
    
    /**
     * Gets the type returned by this class
     *
     * @return Xyster_Type
     */
    public function getReturnedType()
    {
        return Xyster_Type::boolean();
    }
}