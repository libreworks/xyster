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
 * @see Xyster_Orm_Tuplizer_Interface
 */
require_once 'Xyster/Orm/Tuplizer/Interface.php';
/**
 * @see Xyster_Orm_Session_Factory_Interface
 */
require_once 'Xyster/Orm/Session/Factory/Interface.php';
/**
 * A tuplizer for mapping components
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Tuplizer_Component_Interface extends Xyster_Orm_Tuplizer_Interface
{
    /**
     * Gets the value of the parent property
     *
     * @param mixed $component
     * @return mixed
     */
    function getParent( $component );
    
    /**
     * Whether the component managed by this tuplizer have a parent property
     *
     * @return boolean
     */
    function hasParentProperty();
    
    /**
     * Sets the value of the parent property
     *
     * @param mixed $component
     * @param mixed $parent
     */
    function setParent( $component, $parent );
}