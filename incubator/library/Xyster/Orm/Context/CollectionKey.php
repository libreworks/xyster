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
 * Keys a collection in a session
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
final class Xyster_Orm_Context_CollectionKey
{
    private $_hashCode;
    private $_key;
    /**
     * @var Xyster_Orm_Type_Interface
     */
    private $_keyType;
    private $_role;
    
    /**
     * Creates a new collection key
     * 
     * @param Xyster_Orm_Persister_Collection_Interface $persister
     * @param mixed $key
     */
    public function __construct(Xyster_Orm_Persister_Collection_Interface $persister, $key)
    {
        $this->_role = $persister->getRole();
        $this->_key = $key;
        $this->_keyType = $persister->getKeyType();
        $this->_hashCode = Xyster_Type::hash(array($this->_role, $key));
    }
    
    /**
     * Whether this object is equal to the one given
     * 
     * @param object $object
     * @return boolean
     */
    public function equals($object)
    {
        return $this === $object ||
            ($object instanceof Xyster_Orm_Context_CollectionKey &&
                $this->_role == $object->_role &&
                $this->_keyType->isEqual($this->_key, $object->_key));
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
     * Gets the collection role
     * 
     * @return string
     */
    public function getRole()
    {
        return $this->_role;
    }
    
    /**
     * Gets the hash code for the object
     * 
     * @return int
     */
    public function hashCode()
    {
        return Xyster_Type::hash(array($this->_role, $this->_key));
    }
}