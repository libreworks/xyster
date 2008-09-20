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
 * @see Xyster_Orm_Persister_Entity_Loadable_Interface
 */
require_once 'Xyster/Orm/Persister/Entity/Loadable/Interface.php';
/**
 * @see Xyster_Orm_Persister_Joinable_Interface
 */
require_once 'Xyster/Orm/Persister/Joinable/Interface.php';
/**
 * @see Xyster_Orm_Persister_PropertyMapping_Interface
 */
require_once 'Xyster/Orm/Persister/PropertyMapping/Interface.php';
/**
 * Adds operations required by the runtime query language
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Persister_Entity_Queryable_Interface extends Xyster_Orm_Persister_Entity_Loadable_Interface, Xyster_Orm_Persister_Joinable_Interface, Xyster_Orm_Persister_PropertyMapping_Interface
{
    /**
     * Whether the version property should be added to insert statements
     * 
     * @return boolean
     */
    function isVersionPropertyInsertable();
}