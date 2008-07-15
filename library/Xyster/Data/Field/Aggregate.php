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
 * @see Xyster_Data_Field
 */
require_once 'Xyster/Data/Field.php';
/**
 * A field or column that has some aggregate function applied to it
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Data_Field_Aggregate extends Xyster_Data_Field
{
    /**
     * The pattern to match aggregate function fields
     *
     */
    const AGGREGATE_REGEX = '/^(?P<function>AVG|MAX|MIN|COUNT|SUM)\((?P<field>[\w\W]*)\)$/i';
    
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
    
    /**
     * Matches for aggregate functions
     *
     * @param string $haystack
     * @return array
     */
    static public function match( $haystack )
    {
        $matches = array();
        preg_match(self::AGGREGATE_REGEX, trim($haystack), $matches);
        return $matches;
    }
}