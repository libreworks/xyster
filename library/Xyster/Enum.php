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
 * @package   Xyster
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';
 
/**
 * Enumerable type object
 *
 * PHP contains no enum-type class internally, so to mirror the convenience such
 * a class offers, we created the Xyster_Enum.  Enum classes are used to
 * represent set types of things, for instance, if we created a class to
 * represent different operating systems, it might provide enum methods like
 * this:
 *
 * <code>
 * $unix = OperatingSystem::Unix();
 * $win = OperatingSystem::Windows();
 * $mac = OperatingSystem::Mac();
 * echo $unix->getName(); // prints Unix
 * </code>
 *
 * @category  Xyster
 * @package   Xyster
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Enum
{
    /**
     * The enum name 
     * 
     * @var string 
     */
	private $_name;
	/**
	 * The enum value (usually a number, but doesn't have to be)
	 * 
	 * @var mixed
	 */
	private $_value;

	/**
	 * Static cache for factoried enum instances
	 * 
	 * @var array
	 */
	static private $_instances = array();

	/**
	 * Creates a new enum derived class
	 *
	 * @param string $name  Enum name
	 * @param mixed $value	Enum value
	 */
	final protected function __construct( $name, $value )
	{
		$this->_name = $name;
		$this->_value = $value;
	}

	/**
	 * Cannot be cloned
	 *
	 * @magic
	 */
	public function __clone()
	{
		throw new Exception;
	}

	/**
	 * Gets the string name of this enum type
	 *
	 * @return string  Name of this enum
	 */
	public function getName()
	{
		return $this->_name;
	}
	/**
	 * Gets the value of this enum type
	 *
	 * @return mixed  Value of this enum
	 */
	public function getValue()
	{
		return $this->_value;
	}
	/**
	 * Gets details of the object
	 *
	 * @magic
	 * @return string  Details of object
	 */
	public function __toString()
	{
		return get_class($this)." [".$this->_value.",".$this->_name."]";
	}

	/**
	 * Returns the corresponding enum object based on name
	 *
	 * <code>
	 * $color = Xyster_Enum::parse('Colors','red'); // name is case insensitive
	 * </code>
	 *
	 * @param string $className  Name of the Xyster_Enum-derived class to instantiate
	 * @param string $name  Name to parse
	 * @return Xyster_Enum  Translated object
	 * @throws Exception  if $name was not found in $className
	 */
	static public function parse( $className, $name )
	{
		foreach( self::values($className) as $constValue=>$constName ) {
			if ( strcasecmp( $constName, $name ) == 0 ) {
				return self::_factory($className, $constName);
			}
		}
		throw new Exception();
	}
	/**
	 * Returns the corresponding enum object based on value
	 *
	 * <code>
	 * $color = Xyster_Enum::valueOf('Colors',0);
	 * </code>
	 *
	 * @param string $className  Name of the Xyster_Enum-derived class to instantiate
	 * @param string $value  Value to parse
	 * @return Xyster_Enum  Translated object
	 * @throws Exception  if $className isn't derived from Xyster_Enum
	 */
	static public function valueOf( $className, $value )
	{
		foreach( self::values($className) as $constValue=>$constName ) {
			if ( strcasecmp( $constValue, $value ) == 0 ) {
				return self::_factory($className, $constName);
			}
		}
		throw new Exception();
	}
	/**
	 * Gets an array of the name and value pairs available from an enum
	 *
	 * The associative array contains the enum values as keys and the enum
	 * constant names as values.  For example, the Colors enum might return:
	 * <code>
	 * return array( 0=>'Red', 1=>'Orange', 2=>'Yellow', 3=>'Green' );
	 * </code>
	 *
	 * @param string $className
	 * @return array
	 * @throws Exception  if $className isn't derived from Xyster_Enum
	 */
	static public function values( $className )
	{
	    Zend_Loader::loadClass($className);
		if ( !is_subclass_of($className, __CLASS__) ) {
			throw new Exception();
		}
		$rc = new ReflectionClass($className);
		return array_flip($rc->getConstants());
	}

	/**
	 * Factories and returns a singleton enum
	 *
	 * @param string $className
	 * @param mixed $name
	 * @return Xyster_Enum
	 */
	static protected function _factory( $className = null, $name = null )
	{
		if ( $className == null ) {
			$bt = debug_backtrace();
			$className = $bt[1]['class'];
			$name = $bt[1]['function'];
		}
		if ( !isset(self::$_instances[$className][$name]) ) {
			$rc = new ReflectionClass($className);
			self::$_instances[$className][$name] =
			    new $className( $name, $rc->getConstant($name) );
		}
		return self::$_instances[$className][$name];
	}
}
?>