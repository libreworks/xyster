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
 * A listener for entity events
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Entity_Listener
{
    /**
     * Called before a value is set 
     *
     * @param Xyster_Orm_Entity $entity The entity whose value was set
     * @param string $name The name of the field
     * @param mixed $old The old value before the set
     * @param mixed $new The new value after the set
     */
    public function onSetField( Xyster_Orm_Entity $entity, $name, $old, $new )
    {
    }
    
    /**
     * Called before a relation is set
     *
     * If the relation wasn't loaded at the time the new value was set, the old
     * value argument to this method will be null; the old one won't be loaded.
     * 
     * @param Xyster_Orm_Entity $entity The entity whose value was set
     * @param string $name The name of the relation
     * @param Xyster_Orm_Entity|Xyster_Orm_Set $old
     * @param Xyster_Orm_Entity|Xyster_Orm_Set $new
     */
    public function onSetRelation( Xyster_Orm_Entity $entity, $name, $old, $new )
    {
    }
}