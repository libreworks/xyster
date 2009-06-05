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
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Autowiring modes
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Autowire extends Xyster_Enum
{
    const None = 0;
    const ByName = 1;
    const ByType = 2;
    const Constructor = 3;
    
	/**
	 * No autowiring
	 *
	 * @return Xyster_Container_Autowire
	 */
	static public function None()
	{
	   return Xyster_Enum::_factory();
	}
	
	/**
	 * Autowiring by property name
	 *
	 * @return Xyster_Container_Autowire
	 */
	static public function ByName()
	{
	   return Xyster_Enum::_factory();
	}
	
	/**
	 * Autowiring by property type
	 *
	 * @return Xyster_Container_Autowire
	 */
	static public function ByType()
	{
	   return Xyster_Enum::_factory();
	}
	
	/**
	 * Autowiring by constructor arguments
	 *
	 * @return Xyster_Container_Autowire
	 */
	static public function Constructor()
	{
	   return Xyster_Enum::_factory();
	}
}