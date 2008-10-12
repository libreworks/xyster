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
 * Keys an entity in a session by unique property
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Context_EntityUniqueKey
{
    private $_hashCode;
    private $_key;
    /**
     * @var Xyster_Orm_Type_Interface
     */
    private $_keyType;
    private $_name;
    private $_uniqueKeyName;

    /**
     * Creates a new entity unique key
     * 
     * @param string $entityName
     * @param string $uniqueKeyName
     * @param mixed $key
     * @param Xyster_Orm_Type_Interface $keyType
     */
    public function __construct($entityName, $uniqueKeyName, $key, Xyster_Orm_Type_Interface $keyType)
    {
        $this->_name = $entityName;
        $this->_uniqueKeyName = $uniqueKeyName;
        $this->_key = $key;
        $this->_keyType = $keyType;
        $this->_hashCode = Xyster_Type::hash(array($entityName, $uniqueKeyName,
            $key));
    }
    
    /**
     * Whether the object equals the given one
     * 
     * @param object $object
     * @return boolean
     */
    public function equals($object)
    {
        return $this === $object ||
            ($object instanceof Xyster_Orm_Context_EntityUniqueKey &&
                $this->_name == $object->_name &&
                $this->_uniqueKeyName == $object->_uniqueKeyName &&
                $this->_keyType->isEqual($this->_key, $object->_key));
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
     * Gets the key
     * 
     * @return mixed
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Gets the unique key name 
     * 
     * @return string
     */
    public function getUniqueKeyName()
    {
        return $this->_uniqueKeyName;
    }
    
    /**
     * Gets the hash code for this object
     * 
     * @return int
     */
    public function hashCode()
    {
        return $this->_hashCode;
    }
}