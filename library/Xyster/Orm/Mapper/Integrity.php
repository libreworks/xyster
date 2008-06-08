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
 * @see Xyster_Db_ReferentialAction
 */
require_once 'Xyster/Db/ReferentialAction.php';
/**
 * Referential integrity manager
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapper_Integrity
{
    /**
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_factory;
    
    /**
     * @var array
     */
    protected $_defaults = array();
    
    /**
     * Creates a new integrity constraint manager
     *
     * @param Xyster_Orm_Mapper_Factory_Interface $factory
     */
    public function __construct( Xyster_Orm_Mapper_Factory_Interface $factory )
    {
        $this->_factory = $factory;
    }
    
    /**
     * Performs integrity constraints on related entities
     *
     * If database behavior is emulated, we will actually do the constraints 
     * ourselves.  For instance, a cascade delete will cause us to delete all
     * associated entities.  This should be used if the database system doesn't
     * support the behavior.
     * 
     * @param Xyster_Orm_Entity $entity The entity whose constraints should be checked
     * @param boolean $emulate Optional. Whether to emulate database behavior
     * @return array An array of entities that should be refreshed after the delete
     * @throws Xyster_Orm_Mapper_Exception if restrict or no action finds that there are dependants
     */
    public function delete( Xyster_Orm_Entity $entity, $emulate = false )
    {
        $repository = $this->_factory->getManager()->getRepository();
        $toRefreshAfter = array();
        foreach( $this->_factory->getEntityType(get_class($entity))->getRelations() as $name => $relation ) {
            /* @var $relation Xyster_Orm_Relation */
            if ( $relation->getType() == 'many' ) {
                $action = $relation->getOnDelete();
                if (! $action instanceof Xyster_Db_ReferentialAction ) {
                    continue;
                }
                $map = $this->_factory->get($relation->getTo());
                $reverseName = $relation->hasBelongsTo() ?
                    $relation->getReverse()->getName() : null;
                $related = $entity->$name; /* @var $related Xyster_Orm_Set */
                
                // stop deletes if entity is referenced
                $this->_checkDepends($action, $related);
                // cascade the delete
                if ( $action === Xyster_Db_ReferentialAction::Cascade() ) {
                    $repository->removeAll($related);
                    if ( $emulate ) {
                        foreach( $related as $relatedEntity ) {
                            $map->delete($relatedEntity);
                        }
                    }
                // set fields to default values
                } else if ( $action === Xyster_Db_ReferentialAction::SetDefault()
                    && $reverseName ) {
                    foreach( $related as $relatedEntity ) {
                        if ( $emulate ) {
                            $relatedEntity->$reverseName = null;
                            foreach( $this->_getFieldDefaults($relation) as $fkeyName => $default ) {
                                $relatedEntity->{'set'.ucfirst($fkeyName)}($default);
                            }
                            $map->save($relatedEntity);
                        } else {
                            $toRefreshAfter[] = $relatedEntity;
                        }
                    }
                // sets to null
                } else if ( $action === Xyster_Db_ReferentialAction::SetNull()
                    && $reverseName ) {
                    foreach( $related as $relatedEntity ) {
                        if ( $emulate ) {
                            $relatedEntity->$reverseName = null;
                            $map->save($relatedEntity);
                        } else {
                            $toRefreshAfter[] = $relatedEntity;
                        }
                    }
                }
            }
        }
        return $toRefreshAfter;
    }
    
    /**
     * Performs integrity constraints on related entities
     *
     * If database behavior is emulated, we will actually do the constraints 
     * ourselves.  For instance, a cascade update will cause us to update all
     * associated entities.  This should be used if the database system doesn't
     * support the behavior.
     * 
     * @param Xyster_Orm_Entity $entity The entity whose constraints should be checked
     * @param boolean $emulate Optional. Whether to emulate database behavior
     * @return array An array of entities that should be refreshed after the update
     * @throws Xyster_Orm_Mapper_Exception if restrict or no action finds that there are dependants 
     */
    public function update( Xyster_Orm_Entity $entity, $emulate = false )
    {
        $toRefreshAfter = array();
        $updatedKey = $entity->getPrimaryKey() != $entity->getPrimaryKey(true);
        foreach( $this->_factory->getEntityType(get_class($entity))->getRelations() as $name=>$relation ) {
            /* @var $relation Xyster_Orm_Relation */
            if ( $relation->isCollection() && $updatedKey ) {
                $action = $relation->getOnUpdate();
                if (! $action instanceof Xyster_Db_ReferentialAction ) {
                    continue;
                }
                $map = $this->_factory->get($relation->getTo());
                $reverseName = $relation->hasBelongsTo() ?
                    $relation->getReverse()->getName() : null;
                $related = $entity->$name; /* @var $related Xyster_Orm_Set */
                
                // stop deletes if entity is referenced
                $this->_checkDepends($action, $related);

                foreach( $related as $relatedEntity ) {
                    if ( $emulate ) { 
                        // cascade the update
                        if ( $action === Xyster_Db_ReferentialAction::Cascade() ) {
                            $relatedEntity->$reverseName = $entity;
                        // set fields to default values
                        } else if ( $action === Xyster_Db_ReferentialAction::SetDefault()
                            && $reverseName ) {
                            $relatedEntity->$reverseName = null;
                            foreach( $this->_getFieldDefaults($relation) as $fkeyName => $default ) {
                                $relatedEntity->{'set'.ucfirst($fkeyName)}($default);
                            }
                        // sets to null
                        } else if ( $action === Xyster_Db_ReferentialAction::SetNull()
                            && $reverseName ) {
                            $relatedEntity->$reverseName = null;    
                        }
                        $map->save($relatedEntity);
                    } else {
                        $toRefreshAfter[] = $relatedEntity;
                    }
                }
            }
        }
        return $toRefreshAfter;
    }
    
    /**
     * Checks dependencies 
     *
     * @param Xyster_Db_ReferentialAction $action
     * @param Xyster_Orm_Set $related
     * @throws Xyster_Orm_Mapper_Exception
     */
    final protected function _checkDepends( Xyster_Db_ReferentialAction $action, Xyster_Orm_Set $related )
    {
        // stop deletes if entity is referenced
        if ( ( $action === Xyster_Db_ReferentialAction::Restrict() ||
            $action === Xyster_Db_ReferentialAction::NoAction() ) && count($related) ) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('Cannot delete entity because others depend on it');
        }
    }
    
    /**
     * Gets the defaults for a set of foreign keys
     *
     * @param Xyster_Orm_Relation $relation
     * @return array
     */
    protected function _getFieldDefaults( Xyster_Orm_Relation $relation )
    {
        $defaults = array();
        $reverse = $relation->getReverse();
        $key = ( $reverse instanceof Xyster_Orm_Relation ) ?
            $reverse->getFrom() . ':' . $reverse->getName() : null;
        if ( $key ) {
            if ( !isset($this->_defaults[$key]) ) {
                $type = $this->_factory->getEntityType($reverse->getFrom());
                $fkeyNames = $reverse->getId();
                $localDefaults = array();
                foreach( $fkeyNames as $fkeyName ) {
                    $localDefaults[$fkeyName] = $type->getField($fkeyName)->getDefault();
                }
                $this->_defaults[$key] = $localDefaults;
            }
            $defaults = $this->_defaults[$key];
        }
        return $defaults;
    }
}