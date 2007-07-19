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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';
/**
 * The entity/mapper/collection class loader for the Orm layer
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Loader
{
    /**
     * Paths where entities, mappers, and sets are stored
     * 
     * @var array
     */
    static protected $_paths = array();

    private function __construct()
    {
    }

    /**
     * Adds a path to where class files for entities can be found
     *
     * @param string $path
     */
    public static function addPath( $path )
    {
        $path        = rtrim($path, '/');
        $path        = rtrim($path, '\\');
        $path       .= DIRECTORY_SEPARATOR;
        
        if ( @!is_dir($path) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception("The path '$path' does not exist'");
        }
        
        self::$_paths[$path] = $path; // no need for dups
    }
    
    /**
     * spl_autoload() suitable implementation for supporting class autoloading.
     *
     * Attach to spl_autoload() using the following:
     * <code>
     * spl_autoload_register(array('Xyster_Orm', 'autoload'));
     * </code>
     * 
     * @param string $class 
     * @return mixed string class name on success; false on failure
     */
    public static function autoload($class)
    {
        try {
            self::loadClass($class);
            return $class;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Tries to load the class in one of the paths defined for entities
     *
     * @param string $className
     * @return string the class name loaded
     * @throws Xyster_Orm_Exception if the class file cannot be loaded
     * @throws Xyster_Orm_Exception if the file is found but no class with the name given is defined
     */
    public static function loadClass( $className )
    {
        if ( class_exists($className, false) ) {
            return $className;
        }

        $dirs = self::$_paths;
        $file = $className . '.php';
        
        try {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadFile($file, $dirs, true);
        } catch (Zend_Exception $e) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Cannot load class "' . $className . '"');
        }

        if (!class_exists($className,false)) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Invalid class ("' . $className . '")');
        }

        return $className;
    }

    /**
     * Loads the class and makes sure it's a Xyster_Orm_Entity
     *
     * @param string $className
     * @throws Xyster_Orm_Exception if the class loaded is not a derivitive of Xyster_Orm_Entity
     */
    public static function loadEntityClass( $className )
    {
        self::loadClass($className);

        if (!($className instanceof Xyster_Orm_Entity) &&
            !is_subclass_of($className, 'Xyster_Orm_Entity')) {
            throw new Xyster_Orm_Exception("'" . $className . "' is not a subclass of Xyster_Orm_Entity");
        }
    }

    /**
     * Loads the mapper class for the entity class given
     * 
     * To load the 'PersonMapper' class, the $className parameter should just be 
     * 'Person'.
     * 
     * @param string $className the name of the entity class
     * @return string the class name
     * @throws Xyster_Orm_Exception if the class loaded is not a derivitive of Xyster_Orm_Mapper_Interface
     */
    public static function loadMapperClass( $className )
    {
        self::loadEntityClass($className);

        $mapper = $className . 'Mapper';
        
        self::loadClass($mapper);
        
        if (!($mapper instanceof Xyster_Orm_Mapper_Interface) &&
            !is_subclass_of($mapper, 'Xyster_Orm_Mapper_Abstract')) {
            throw new Xyster_Orm_Exception("'" . $mapper . "' is not a subclass of Xyster_Orm_Mapper_Interface");
        }
        
        return $mapper;
    }

    /**
     * Loads the set class for the entity class given
     *
     * @param string $className the name of the set class
     * @param boolean $autoSuffix whether to append 'Set' to the end of the class
     * @throws Xyster_Orm_Exception if the class loaded is not a derivitive of Xyster_Orm_Set
     * @return string the class name
     */
    public static function loadSetClass( $className, $autoSuffix = true )
    {
        $set = ( $autoSuffix ) ? $className . 'Set' : $className;
        self::loadClass($set);
        
        if (!($set instanceof Xyster_Orm_Set) &&
            !is_subclass_of($set, 'Xyster_Orm_Set')) {
            throw new Xyster_Orm_Exception("'" . $set . "' is not a subclass of Xyster_Orm_Set");
        }
        
        return $set;
    }
    
    /**
     * Register {@link autoload()} with spl_autoload()
     * 
     * @throws Zend_Exception if spl_autoload() is not found or if the specified class does not have an autoload() method.
     */
    public static function registerAutoload()
    {
        Zend_Loader::registerAutoload(__CLASS__);
    }
}