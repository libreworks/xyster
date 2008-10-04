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
 * @see Xyster_Orm_Type_Interface
 */
require_once 'Xyster/Orm/Type/Interface.php';
/**
 * A type that represents a relationship between entities
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Type_Association extends Xyster_Orm_Type_Interface
{
    /**
     * Gets the associated entity name
     * @param Xyster_Orm_Session_Factory_Interface $factory
     * @return string
     */
    function getAssociatedEntityName( Xyster_Orm_Session_Factory_Interface $factory );
    
    /**
     * Gets the persister for this association; class or collection
     * 
     * @param Xyster_Orm_Session_Factory_Interface $factory
     * @return Xyster_Orm_Persister_Joinable_Interface
     */
    function getAssociatedJoinable( Xyster_Orm_Session_Factory_Interface $factory );
    
    /**
     * Gets the foreign key direction
     * 
     * @return Xyster_Orm_Engine_ForeignKeyDirection
     */
    function getForeignKeyDirection();
    
    /**
     * Gets the join key from the owning entity (null if the id)
     * 
     * @return string
     */
    function getLeftPropertyName();

    /**
     * Gets the extra SQL used in the join ON clause
     * @param string $alias
     * @param Xyster_Orm_Session_Factory_Interface $factory
     * @param Xyster_Collection_Map_Interface $filters
     * @return string
     */
    function getOnFilter($alias, Xyster_Orm_Session_Factory_Interface $factory, Xyster_Collection_Map_Interface $filters);
    
    /**
     * Gets the name the unique property in the associated entity used to join
     * 
     * @return string
     */
    function getRightPropertyName();
    
    /**
     * Whether this association is dirty checked even if no updateable columns
     *  
     * @return boolean
     */
    function isAlwaysDirtyChecked();
    
    /**
     * Whether to use the pkey of the owning entity in the join
     * 
     * @return boolean
     */
    function useLeftPrimaryKey();
}