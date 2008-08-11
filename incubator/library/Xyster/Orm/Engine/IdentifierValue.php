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
 * Distinguishes unsaved identifier values from persisted ones
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Engine_IdentifierValue
{
    /**
     * @var mixed
     */
    protected $_value;
    
    /**
     * One of 'ANY', 'NONE', 'NULL', or 'UNDEFINED'
     * @var string
     */
    protected $_preset;
    
    /**
     * @var array
     */
    static protected $_presets = array();
    
    /**
     * Creates a new identifiervalue
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
        return ( $this->_preset == 'ANY' || $this->_preset == 'NONE' ) ?
            $current : $this->_value;
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
            case 'ANY':
                return true;
            case 'NONE':
                return false;
            case 'UNDEFINED':
                return null;
            case 'NULL':
            default:
                return $id === null ||
                    Xyster_Type::areDeeplyEqual($id, $this->_value);
        }
    }
    
    /**
     * Always assume the value passed is unsaved
     *
     * @return Xyster_Orm_Engine_IdentifierValue
     */
    public static function Any()
    {
        if ( !isset(self::$_presets['ANY']) ) {
            self::$_presets['ANY'] = new self;
            self::$_presets['ANY']->_preset = 'ANY';
        }
        return self::$_presets['ANY'];
    }
    
    /**
     * Never assume the value passed is unsaved
     *
     * @return Xyster_Orm_Engine_IdentifierValue
     */
    public static function None()
    {
        if ( !isset(self::$_presets['NONE']) ) {
            self::$_presets['NONE'] = new self;
            self::$_presets['NONE']->_preset = 'NONE';
        }
        return self::$_presets['NONE'];
    }
    
    /**
     * The value is unsaved if null
     *
     * @return Xyster_Orm_Engine_IdentifierValue
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
     * @return Xyster_Orm_Engine_IdentifierValue
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
     * Factories an identifiervalue based on name
     *
     * @param string $unsaved
     * @return Xyster_Orm_Engine_IdentifierValue
     */
    public static function factory( $unsaved )
    {
        switch( $unsaved ) {
            case "any":
                return self::Any();
            case "none":
                return self::None();
            case "undefined":
                return self::Undefined();
            case "null":
            default:
                return self::Null();
        }
    }
}