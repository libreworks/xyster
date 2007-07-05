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
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * A field or column that has some aggregate function applied to it
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Field_Aggregate extends Xyster_Data_Field
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
    protected function __construct( Xyster_Data_Aggregate $function, $name, $alias = null )
    {
        parent::__construct($name, $alias);
        $this->_function = $function;
    }

    /**
     * Evaluates the reference for the given object
     *
     * @param mixed $object
     * @return mixed
     */
    public function evaluate( $object )
    {
        return ( $object instanceof Xyster_Data_Set ) ?
            $object->aggregate($this) : parent::evaluate($object);
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

    /**
     * String representation of this object
     *
     * @magic
     * @return string
     */
    public function __toString()
    {
        return $this->_function->getValue() . '(' . parent::__toString() . ')';
    }
}