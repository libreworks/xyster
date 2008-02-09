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
 * @version   $Id$
 */
/**
 * @see Xyster_Data_Clause
 */
require_once 'Xyster/Data/Clause.php';
/**
 * @see Xyster_Data_Sort
 */
require_once 'Xyster/Data/Sort.php';
/**
 * A sort clause
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Sort_Clause extends Xyster_Data_Clause
{
	/**
	 * @var Xyster_Type
	 */
	static private $_type;
	
	/**
	 * Creates a new Sort clause
	 * 
	 * @param Xyster_Data_Symbol $symbol The symbol or clause to add to this one
	 */
	public function __construct( Xyster_Data_Symbol $symbol = null )
	{
		if ( !self::$_type instanceof Xyster_Type ) {
			self::$_type = new Xyster_Type('Xyster_Data_Sort');
		}
		parent::__construct(self::$_type, $symbol);
	}
}