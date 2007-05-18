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
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
/**
 * A field or column that has some aggregate function applied to it
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Field_Aggregate
{
    /**
     * @var Xyster_Data_Aggregate
     */
   	protected $_function;

	/**
	 * Creates a new Aggregate Field
	 *
	 * @param Xyster_Data_Aggregate $function The aggregate function applied
	 * @param string $name  The field name (be it a property, column, whatever)
	 * @param string $alias  The alias for this field
	 */
	protected function __construct( Xyster_Data_Aggregate $function, $name, $alias=null )
	{
		parent::__construct($name,$alias);
		$this->_function = $function;
	}

	/**
	 * Gets the aggregate function associated with this field
	 *
	 * @return Xyster_Data_Aggregate The assigned aggregate function
	 */
	public function getFunction()
	{
		return $this->_function;
	}
}