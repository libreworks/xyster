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
 * @see Xyster_Orm_Mapper_Factory_Interface
 */
require_once 'Xyster/Orm/Mapper/Factory/Interface.php';
/**
 * A simple factory for creating mappers
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Mapper_Factory_Abstract implements Xyster_Orm_Mapper_Factory_Interface
{
    /**
     * The manager containing this factory
     *
     * @var Xyster_Orm_Manager
     */
    protected $_manager;
    
    /**
     * A place to cache instantiated mappers 
     * 
     * @var array
     */
    protected $_mappers = array();
    
    /**
     * Gets the entity meta for a given class
     * 
     * @param string $className The name of the entity class
     * @return Xyster_Orm_Entity_Type The meta object
     */
    public function getEntityType( $className )
    {
        return $this->get($className)->getEntityType();
    }
    
    /**
     * Gets the manager containing this factory
     *
     * @return Xyster_Orm_Manager
     */
    public function getManager()
    {
        return $this->_manager;
    }
    
    /**
     * Sets the manager that contains this factory
     *
     * @param Xyster_Orm_Manager $manager
     */
    public function setManager( Xyster_Orm_Manager $manager )
    {
        $this->_manager = $manager;
    }
}