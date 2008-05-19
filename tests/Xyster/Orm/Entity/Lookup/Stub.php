<?php
/**
 * @see Xyster_Orm_Entity_Lookup_Interface
 */
require_once 'Xyster/Orm/Entity/Lookup/Interface.php';

class Xyster_Orm_Entity_Lookup_Stub implements Xyster_Orm_Entity_Lookup_Interface
{
    public $entityType;
    public $name;
    public $returnType;
    
    /**
     * Gets the entity type supported by this lookup
     *
     * @return Xyster_Orm_Entity_Type
     */
    public function getEntityType()
    {
        return $this->entityType;
    }
    
    /**
     * Gets the field name of this lookup on the entity class 
     *
     * @return string The field name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Gets the type of object or value returned by this lookup
     *
     * @return Xyster_Type The value type
     */
    public function getType()
    {
        return $this->returnType;
    }
    
    /**
     * Gets the lookup value for the entity given
     *
     * @param Xyster_Orm_Entity $entity
     * @return mixed The lookup value or object
     */
    public function get( Xyster_Orm_Entity $entity )
    {
    }
    
    /**
     * Sets any fields affected by changing the value of this lookup
     * 
     * @param Xyster_Orm_Entity $entity
     * @param mixed $value The new value for the lookup
     */
    public function set( Xyster_Orm_Entity $entity, $value )
    {
    }
}