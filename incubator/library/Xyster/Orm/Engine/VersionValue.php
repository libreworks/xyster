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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * Distinguishes unsaved version values from persisted ones
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Engine_VersionValue
{
    /**
     * @var mixed
     */
    protected $_value;
    
    /**
     * One of 'NEGATIVE', 'NULL', or 'UNDEFINED'
     * @var string
     */
    protected $_preset;
    
    /**
     * @var array
     */
    static protected $_presets = array();
    
    /**
     * Creates a new versionvalue
     *
     * @param mixed $value
     */
    public function __construct( $value = null )
    {
        $this->_value = $value;
    }
    
    /**
     * Gets the default value in light of the current one
     *
     * @param mixed $current
     * @return mixed
     */
    public function getDefaultValue( $current )
    {
        if ( $this->_preset == 'NEGATIVE' ) {
            return -1;
        } else if ( $this->_preset == 'UNDEFINED' ) {
            return $current;
        } else {
            return $this->_value;
        }
    }
    
    /**
     * Whether the value supplied is an unsaved value
     *
     * @param mixed $id
     * @return boolean
     */
    public function isUnsaved( $id )
    {
        switch ( $this->_preset ) {
            case 'NEGATIVE':
                return $id === null || $id < 0;
            case 'UNDEFINED':
                return null;
            case 'NULL':
            default:
                return $id === null ||
                    Xyster_Type::areDeeplyEqual($id, $this->_value);
        }
    }
    
    /**
     * Assume unsaved if version is negative
     *
     * @return Xyster_Orm_Engine_VersionValue
     */
    public static function Negative()
    {
        if ( !isset(self::$_presets['NEGATIVE']) ) {
            self::$_presets['NEGATIVE'] = new self;
            self::$_presets['NEGATIVE']->_preset = 'NEGATIVE';
        }
        return self::$_presets['NEGATIVE'];
    }
    
    /**
     * The value is unsaved if null
     *
     * @return Xyster_Orm_Engine_VersionValue
     */
    public static function Null()
    {
        if ( !isset(self::$_presets['NULL']) ) {
            self::$_presets['NULL'] = new self;
            self::$_presets['NULL']->_preset = 'NULL';
        }
        return self::$_presets['NULL'];
    }
    
    /**
     * Don't make assumptions about the saved state
     *
     * @return Xyster_Orm_Engine_VersionValue
     */
    public static function Undefined()
    {
        if ( !isset(self::$_presets['UNDEFINED']) ) {
            self::$_presets['UNDEFINED'] = new self;
            self::$_presets['UNDEFINED']->_preset = 'UNDEFINED';
        }
        return self::$_presets['UNDEFINED'];
    }
    
    /**
     * Factories a versionvalue based on name
     *
     * @param string $value The setting name
     * @return Xyster_Orm_Engine_VersionValue
     */
    public static function factory( $value )
    {
        switch( $value ) {
            case "negative":
                return self::Negative();
            case "undefined":
                return self::Undefined();
            case "null":
            default:
                return self::Null();
        }
    }
}