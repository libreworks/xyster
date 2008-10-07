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
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * Keys an entity in a session by identifier
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
final class Xyster_Orm_Engine_EntityKey
{
    private $_batchLoadable = false;
    private $_hashCode;
    private $_id;
    /**
     * @var Xyster_Orm_Type_Interface
     */
    private $_idType;
    private $_name;
    private $_rootName;
    
    /**
     * Creates a new entity key object
     * 
     * @param mixed $id
     * @param Xyster_Orm_Persister_Entity_Interface $persister
     */
    public function __construct($id, Xyster_Orm_Persister_Entity_Interface $persister)
    {
        $this->_id = $id;
        $this->_rootName = $persister->getRootEntityName();
        $this->_name = $persister->getEntityName();
        $this->_idType = $persister->getIdType();
        $this->_batchLoadable = $persister->isBatchLoadable();
        $this->_hashCode = Xyster_Type::hash(array($this->_rootName, $id));
    }
    
    /**
     * Whether this object equals the one given
     * 
     * @param object $object
     * @return boolean
     */
    public function equals($object)
    {
        return $this === $object ||
            ($object instanceof Xyster_Orm_Engine_EntityKey &&
                $this->_rootName == $object->_rootName &&
                $this->_idType->isEqual($this->_id, $object->_id));
    }
    
    /**
     * Gets the entity name
     * 
     * @return string
     */
    public function getEntityName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the entity identifier
     * 
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Gets the hash code
     * 
     * @return int
     */
    public function hashCode()
    {
        return $this->_hashCode;
    }
    
    /**
     * Gets whether the entity is batch loadable
     * 
     * @return boolean
     */
    public function isBatchLoadable()
    {
        return $this->_batchLoadable;
    }
}