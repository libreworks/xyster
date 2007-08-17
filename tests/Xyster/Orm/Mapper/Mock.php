<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   UnitTests
 * @subpackage Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * @see Xyster_Orm_Mapper_Abstract
 */
require_once 'Xyster/Orm/Mapper/Abstract.php';

/**
 * A mock mapper
 *
 */
abstract class Xyster_Orm_Mapper_Mock extends Xyster_Orm_Mapper_Abstract
{
    protected $_domain = 'mock';
    protected $_saved = array();
    protected $_deleted = array();
    
    public function find( $criteria )
    {
        $criteria = $this->_buildCriteria($criteria);
        
        /* @var $criteria Xyster_Data_Criterion */
        foreach( $this->getAll() as $bug ) {
            if ( $criteria->evaluate($bug) ) {
                return $bug;
            }
        }
    }
    
    public function findAll( $criteria, $sorts = null )
    {
        $set = $this->getSet($this->getAll());
        $set->filter($this->_buildCriteria($criteria));
        $set->baseline();
        return $set;
    }
    
    public function getAll( array $ids = null )
    {
	    $orWhere = array();
	    if ( !$ids ) {
	        $ids = array();
	    }

        foreach( $ids as $id ) {
    	    $id = $this->_checkPrimaryKey($id);
            $orWhere[] = $this->_buildCriteria($id);
        }

	    $where = ( count($orWhere) ) ?
	        Xyster_Data_Junction::fromArray('OR', $orWhere) : null;
	        
	    $set = $this->getSet();
	    
	    foreach( $this->_getData() as $entity ) {
	        if ( $where ) {
	            if ( $where->evaluate($entity) ) {
	                $set->add($this->_create($entity));
	            }
	        } else {
	            $set->add($this->_create($entity));
	        }
	    }
	    
	    $set->baseline();
	    return $set;
    }
    
    public function getJoined( Xyster_Orm_Entity $entity, Xyster_Orm_Relation $relation )
    {
        return $this->_factory->get($relation->getTo())->getSet();
    }
    
    public function query( Xyster_Orm_Query $query )
    {
        $set = $this->getAll();
        
        if ( count($query->getBackendWhere()) ) {
            $set->filter(Xyster_Data_Criterion::fromArray('AND', $query->getBackendWhere()));
        }
        
        if ( !$query->isRuntime() && count($query->getOrder()) ) {
            $set->sortBy($query->getOrder());
        }
        
        if ( !$query->isRuntime() && $query instanceof Xyster_Orm_Query_Report ) {
            require_once 'Xyster/Data/Set.php';
            return new Xyster_Data_Set($set);
        } else {
            $set->baseline();
        }
        
        return $set;
    }
    
    public function refresh( Xyster_Orm_Entity $entity )
    {
        $criteria = $entity->getPrimaryKeyAsCriterion(true);
        foreach( $this->getAll() as $currentEntity ) {
            if ( $criteria->evaluate($currentEntity) ) {
                $entity->import($currentEntity->getBase());
                return;
            }
        }
    }
    
    public function save( Xyster_Orm_Entity $entity )
    {
        parent::save($entity);
        $this->_saved[] = $entity;
    }
    
    public function wasDeleted( Xyster_Orm_Entity $entity )
    {
        return in_array($entity->getPrimaryKeyAsCriterion(), $this->_deleted);
    }
    
    public function wasSaved( Xyster_Orm_Entity $entity )
    {
        return in_array($entity, $this->_saved, true);
    }
    
    abstract protected function _getData();
    
    protected function _delete( Xyster_Data_Criterion $where )
    {  
        $this->_deleted[] = $where;
    }
    
    protected function _insert( Xyster_Orm_Entity $entity )
    {   
    }
    
    protected function _joinedInsert( Xyster_Orm_Set $set )
    {
    }
    
    protected function _joinedDelete( Xyster_Orm_Set $set )
    {
    }
    
    protected function _update( Xyster_Orm_Entity $entity )
    {
    }
}