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
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
/**
 * @see Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Aggregate function enumerated type
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Aggregate extends Xyster_Enum
{
	const Average = "AVG";
	const Count = "COUNT";
	const Maximum = "MAX";
	const Minimum = "MIN";
	const Sum = "SUM";

	/**
	 * Uses the Average function
	 *
	 * @return Xyster_Data_Aggregate
	 */
	static public function Average()
	{
	   return Xyster_Enum::_factory();
	}
	
	/**
	 * Uses the Count function
	 *
	 * @return Xyster_Data_Aggregate
	 */
	static public function Count()
    {
        return Xyster_Enum::_factory();
    }
    
	/**
	 * Uses the Maximum function
	 *
	 * @return Xyster_Data_Aggregate
	 */
	static public function Maximum()
	{
	   return Xyster_Enum::_factory();
	}
	
    /**
	 * Uses the Minimum function
	 *
	 * @return Xyster_Data_Aggregate
	 */
	static public function Minimum()
	{
	   return Xyster_Enum::_factory();
	}
	
	/**
	 * Uses the Sum function
	 *
	 * @return Xyster_Data_Aggregate
	 */
	static public function Sum()
	{
	   return Xyster_Enum::_factory();
	}
}