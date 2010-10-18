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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace Xyster\Data\Symbol;
/**
 * A field or column that has some aggregate function applied to it
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class AggregateField extends Field
{
    /**
     * The pattern to match aggregate function fields
     */
    const AGGREGATE_REGEX = '/^(?P<function>AVG|MAX|MIN|COUNT|SUM)\((?P<field>[\w\W]*)\)$/i';
    
    /**
     * @var Aggregate
     */
    protected $_function;
    
    /**
     * Creates a new Aggregate Field
     *
     * @param Aggregate $function The aggregate function applied
     * @param string $name  The field name (be it a property, column, whatever)
     * @param string $alias  The alias for this field
     */
    protected function __construct( Aggregate $function, $name, $alias = null )
    {
        parent::__construct($name, $alias);
        $this->_function = $function;
    }
    
    /**
     * Gets the aggregate function associated with this field
     *
     * @return Aggregate The assigned aggregate function
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