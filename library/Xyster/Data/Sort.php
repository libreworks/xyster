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
 * A struct that holds a field and a direction
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Sort
{
    /**
     * The direction of the sort, 'ASC' or 'DESC'
     * 
     * @var string
     */
	private $_direction;
	/**
	 * @var Xyster_Data_Field
	 */
	private $_field;

	/**
	 * Creates a new Sort
	 * 
	 * @param Xyster_Data_Field|string $field
	 * @param string $direction
	 */
	private function __construct( $field, $direction )
	{
		$this->_direction = $direction;
		if (! $field instanceof Xyster_Data_Field) {
		    require_once 'Xyster/Data/Field.php';
			$field = Xyster_Data_Field::named($field);
		}
		$this->_field = $field;
	}

	/**
	 * Gets the field
	 *
	 * @return Xyster_Data_Field
	 */
	public function getField()
	{
		return $this->_field;
	}
	/**
	 * Gets the sort direction
	 *
	 * @return string
	 */
	public function getDirection()
	{
		return $this->_direction;
	}
	/**
	 * Returns the string syntax for this Sort
	 *
	 * @magic
	 * @return string
	 */
	public function __toString()
	{
		return $this->_field->getName() . ' ' . $this->_direction;
	}

	/**
	 * Create a new ascending sort for the column specified
	 *
	 * @param string $field
	 * @return Xyster_Data_Sort
	 */
	static public function asc( $field )
	{
		return new Xyster_Data_Sort( $field, 'ASC' );
	}
	/**
	 * Create a new descending Sort for the column specified
	 *
	 * @param string $field
	 * @return Xyster_Data_Sort
	 */
	static public function desc( $field )
	{
		return new Xyster_Data_Sort( $field, 'DESC' );
	}
}