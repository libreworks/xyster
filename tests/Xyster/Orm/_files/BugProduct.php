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
 * @package   UnitTests
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * @see Xyster_Orm_Entity
 */
require_once 'Xyster/Orm/Entity.php';

/**
 * The 'BugProduct' entity.
 * 
 * Normally, Xyster_Orm doesn't need entities to represent many-to-many tables,
 * but we needed an entity that had multiple primary keys for testing.
 */
class BugProduct extends Xyster_Orm_Entity
{
}