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
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Db_Gateway_TableBuilder_Index
 */
require_once 'Xyster/Db/Gateway/TableBuilder/Index.php';
/**
 * A unique index for the tablebuilder
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Gateway_TableBuilder_Unique extends Xyster_Db_Gateway_TableBuilder_Index
{
	/**
	 * Creates a new unique index
	 *
	 * @param array $columns
     * @param string $name
	 */
	public function __construct( array $columns, $name=null )
	{
		parent::__construct($columns, $name, false);
	}
}