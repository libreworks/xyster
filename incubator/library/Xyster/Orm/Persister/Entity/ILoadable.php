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
 * @see Xyster_Orm_Persister_Entity_Interface
 */
require_once 'Xyster/Orm/Persister/Entity/Interface.php';
/**
 * A persister that can be loaded using the Loader package
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_Entity_Loadable_Interface extends Xyster_Orm_Persister_Entity_Interface
{
    /**
     * Gets the identifier columns names
     * 
     * @return array
     */
    function getIdColumnNames();
    
    /**
     * Gets the column names for this property
     *
     * The name parameter can either be an integer (the numeric offset) or a
     * string property name.
     *
     * @param mixed $name
     * @return array
     */
    function getPropertyColumnNames( $name );
}