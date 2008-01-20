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
 * The base ORM plugin object 
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Plugin_Abstract
{
    /**
     * Called prior to an entity being deleted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postDelete( Xyster_Orm_Entity $entity )
    {
    }
    
    /**
     * Called prior to an entity being inserted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postInsert( Xyster_Orm_Entity $entity )
    {
    }
    
    /**
     * Called after a new entity is loaded with values
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postLoad( Xyster_Orm_Entity $entity )
    {
    }
        
    /**
     * Called prior to an entity being updated
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postUpdate( Xyster_Orm_Entity $entity )
    {
    }
    
    /**
     * Called prior to an entity being deleted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function preDelete( Xyster_Orm_Entity $entity )
    {
    }
    
    /**
     * Called prior to an entity being inserted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function preInsert( Xyster_Orm_Entity $entity )
    {
    }
    
    /**
     * Called prior to an entity being updated
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function preUpdate( Xyster_Orm_Entity $entity )
    {
    }
}