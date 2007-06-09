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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Enum for cache types
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Cache extends Xyster_Enum
{
	const Session = 0;
	const Request = 1;
	const Timeout = 2;

	/**
	 * Used when entities will pretty much never change
	 *
	 * This setting allows entities to be cached for the entire length of a
	 * session
	 *
	 * @return Xyster_Orm_Cache
	 */
	static public function Session() { return Xyster_Enum::_factory(); }
	/**
	 * Used when entities should only persist for a single request
	 *
	 * @return Xyster_Orm_Cache
	 */
	static public function Request() { return Xyster_Enum::_factory(); }
	/**
	 * Used when entities can persist for a set amount of time
	 *
	 * The amount of time an entity persists is a value settable through the 
	 * work unit.
	 *
	 * @return Xyster_Orm_Cache
	 */
	static public function Timeout() { return Xyster_Enum::_factory(); }
}