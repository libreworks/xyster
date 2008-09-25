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
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Type_Proxy_Interface
 */
require_once 'Xyster/Type/Proxy/Interface.php';
/**
 * A mediator for setting and getting values from a named field
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Type_Proxy_Builder
{
    protected static $_types = array();
    protected static $_cache = true;
    
    protected $_parent;
    protected $_interfaces;
    protected $_handler;
    protected $_callConstructor = false;
    protected $_className;
    
    /**
     * Creates a proxy class and returns an instance of it
     * 
     * @param array $args Optional. Arguments to pass to the constructor
     * @return Xyster_Type_Proxy_Interface the proxy object created
     */
    public function create( array $args = null )
    {
        $type = $this->createType();
        array_unshift($args, $this->_handler);
        return $type->getClass()->newInstanceArgs($args);
    }
    
    /**
     * Creates a proxy class
     *  
     * @return Xyster_Type
     */
    public function createType()
    {
        $key = '';
        if ( self::usesCache() ) {
            if ( $this->_parent ) {
                $key .= $this->_parent->getName();
            }
            $key .= '|';
            foreach( $this->_interfaces as $iface ) {
                $key .= $iface->getName() . '|';
            }
            if ( $this->_handler ) {
                $key .= spl_object_hash($this->_handler) . '|';
            }
            $key = ( $this->_callConstructor ) ? 'ccy' : 'ccn';
            if ( isset(self::$_types[$key]) ) {
                return self::$_types[$key];
            }
        }
        eval($this->_getClass());
        $type = new Xyster_Type($this->_className);
        if ( self::usesCache() ) {
            self::$_types[$key] = $type;
        }
        return $type;
    }
    
    /**
     * Sets whether the parent constructor is called (default is false)
     * 
     * @param boolean $flag
     * @return Xyster_Type_Proxy_Builder provides a fluent interface
     */
    public function setCallParentConstructor( $flag = true )
    {
        $this->_callConstructor = $flag;
        return $this;
    }
    
    /**
     * Sets the handler used for method callbacks
     * 
     * @param Xyster_Type_Proxy_Handler_Interface $handler
     * @return Xyster_Type_Proxy_Builder provides a fluent interface
     */
    public function setHandler( Xyster_Type_Proxy_Handler_Interface $handler )
    {
        $this->_handler = $handler;
        return $this;
    }
    
    /**
     * Sets any interfaces the generated class should implement
     * 
     * @param array $interfaces An array of Xyster_Type objects
     * @return Xyster_Type_Proxy_Builder provides a fluent interface
     * @throws Xyster_Type_Proxy_Exception if any items in array aren't interfaces
     */
    public function setInterfaces( array $interfaces )
    {
        foreach( $interfaces as $interface ) {
            if ( !$interface instanceof Xyster_Type ||
                !$interface->isObject() ||
                !$interface->getClass()->isInterface() ) {
                require_once 'Xyster/Type/Proxy/Exception.php';
                throw new Xyster_Type_Proxy_Exception('Not an interface: ' . $interface);
            }
            $this->_interfaces[] = $interface;
        }
        return $this;
    }
    
    /**
     * Sets the parent type the generated class should extend
     * 
     * @param Xyster_Type $parent
     * @return Xyster_Type_Proxy_Builder provides a fluent interface
     * @throws Xyster_Type_Proxy_Exception if the type is invalid as a parent
     */
    public function setParent( Xyster_Type $parent )
    {
        if ( !$parent->isObject() || $parent->getClass()->isInterface() || $parent->getClass()->isFinal() ) {
            require_once 'Xyster/Type/Proxy/Exception.php';
            throw new Xyster_Type_Proxy_Exception('Parent type must be a non-final or abstract class');
        }
        $this->_parent = $parent;
        return $this;
    }
    
    /**
     * Whether the builder caches the classes it generates
     * 
     * @return boolean
     */
    public static function usesCache()
    {
        return self::$_cache;
    }
    
    /**
     * Sets whether the builder caches the classes it generates (default true)
     * 
     * @param boolean $flag
     */
    public static function setCache( $flag = true )
    {
        self::$_cache = $flag;
    }
    
    /**
     * Gets the full class source
     * 
     * @return string
     */
    private function _getClass()
    {
        $class = $this->_getClassHead() .
            " private \$_handler;\n" .
            " private \$_class;\n" .
            $this->_getConstructor() . 
            " public function getHandler()\n { return \$this->_handler; }\n";
        $methods = array();
        foreach( $this->_parent->getClass()->getMethods() as $method ) {
            if ( !$method->isConstructor() && !$method->isFinal() ) {
                $methods[$method->getName()] = $this->_getMethodDefinitionFromExisting($method);
            }
        }
        foreach( $this->_interfaces as $interface ) {
            foreach( $interface->getClass()->getMethods() as $method ) {
                if ( !$method->isConstructor() ) {
                    $methods[$method->getName()] = $this->_getMethodDefinitionFromExisting($method);
                }
            }
        }
        $class .= implode("\n", $methods) . "}\n";
        return $class;
    }
    
    /**
     * Gets the string for the proxy head declaration
     * 
     * @return string
     */
    private function _getClassHead()
    {
        $def = 'class ';
        $extends = '';
        $className = 'XysterTypeProxy_';
        if ( $this->_parent ) {
            $className .= $this->_parent->getName() . '_';
            $extends = ' extends ' . $this->_parent->getName();
        }
        $className .= substr(md5(microtime()), 0, 8);
        $this->_className = $className;
        $def .= $className . $extends . ' implements Xyster_Type_Proxy_Interface';
        foreach( $this->_interfaces as $iface ) {
            $def .= ', ' . $iface->getName();
        }
        $def .= " {\n";
        return $def; 
    }
    
    /**
     * Gets the body of the constructor method
     * 
     * @return string
     */
    private function _getConstructor()
    {
        $def = '';
        $body = "\$this->_handler = \$handler;\n" . 
            "\$this->_class = new ReflectionClass(__CLASS__);\n";
        $decl = "%s function __construct(Xyster_Type_Proxy_Handler_Interface " .
            "\$handler%s) {\n";
        
        if ( $this->_parent && $this->_parent->getClass()->getConstructor() ) {
            $constructor = $this->_parent->getClass()->getConstructor();
            if ( !$constructor->isFinal() ) {
                $visibility = 'public';
                if ($constructor->isPrivate()) {
                    $visibility = 'private';
                } else if ($constructor->isProtected()) {
                    $visibility = 'protected';
                }
                $params = '';
                if ( $this->_callConstructor ) {
                    $params = $this->_getMethodParameters($constructor);
                    $paramNames = array();
                    if ( $params ) {
                        $params = ', ' . $params;
                        foreach( $constructor->getParameters() as $param ) {
                            $paramNames[] = '$' . $param->getName();
                        }
                    }
                    $body .= "parent::__construct(" . implode(',', $paramNames) . ");\n";
                }
                $def .= sprintf($decl, $visibility, $params);
            }
        } else {
            $def .= sprintf($decl, 'public', '');
        }
        $def .= $body;
        $def .= "}\n";        
        return $def;
    }

    /**
     * Gets the method definition from a ReflectionMethod object
     * 
     * @param ReflectionMethod $method
     * @return string
     */
    private function _getMethodDefinitionFromExisting(ReflectionMethod $method)
    {
        /*
         * This method body was taken from PHPUnit_Framework_MockObject_Mock,
         * specifically the 'generateMethodDefinitionFromExisting' method.
         * Used under compatible BSD license.
         * Copyright (c) 2002-2008, Sebastian Bergmann <sb@sebastian-bergmann.de> 
         */
        if ($method->isPrivate()) {
            $modifier = 'private';
        } else if ($method->isProtected()) {
            $modifier = 'protected';
        } else {
            $modifier = 'public';
        }

        if ($method->isStatic()) {
            $modifier .= ' static';
        }

        if ($method->returnsReference()) {
            $reference = '&';
        } else {
            $reference = '';
        }

        return $this->_getMethodDefinition(
          $method->getDeclaringClass()->getName(),
          $method->getName(),
          $modifier,
          $reference,
          $this->_getMethodParameters($method)
        );
    }

    /**
     * Gets the actual method definition
     * 
     * @param string $className
     * @param string $methodName
     * @param string $modifier
     * @param string $reference
     * @param string $parameters
     * @return string
     */
    private function _getMethodDefinition($className, $methodName, $modifier, $reference = '', $parameters = '')
    {
        $parent = $this->_parent;
        $parentMethod = 'null';
        if ( $parent ) {
            $class = $parent->getClass();
            if ( $class->hasMethod($methodName) &&
                !$class->getMethod($methodName)->isAbstract() ) {
                $parentMethod = 'new ReflectionMethod("'.$class->getName().'", __FUNCTION__)';
            }
        }
        $handlerSource = '';
        if ( $this->_handler ) {
            $handlerSource =
                "        return \$this->_handler->invoke(\$this,\n" .
                "            \$this->_class->getMethod(__FUNCTION__),\n" .
                "            \$args,\n" .
                "            %s);\n"; 
        }
        return sprintf(
          "\n    %s function %s%s(%s) {\n" .
          "        \$args = func_get_args();\n" .
          $handlerSource . 
          "    }\n",
          $modifier,
          $reference,
          $methodName,
          $parameters,
          $parentMethod
        );
    }
    
    /**
     * Gets the method parameter declaration
     * 
     * @param ReflectionMethod $method
     * @return string
     */
    private function _getMethodParameters( ReflectionMethod $method )
    {
        /*
         * This method body was taken from PHPUnit_Framework_MockObject_Mock,
         * specifically the 'generateMethodDefinition' method.
         * Used under compatible BSD license.
         * Copyright (c) 2002-2008, Sebastian Bergmann <sb@sebastian-bergmann.de> 
         */
        $parameters = array();

        foreach ($method->getParameters() as $parameter) {
            $name     = '$' . $parameter->getName();
            $typeHint = '';

            if ($parameter->isArray()) {
                $typeHint = 'array ';
            } else {
                try {
                    $class = $parameter->getClass();
                }

                catch (ReflectionException $e) {
                    $class = FALSE;
                }

                if ($class) {
                    $typeHint = $class->getName() . ' ';
                }
            }

            $default = '';

            if ($parameter->isDefaultValueAvailable()) {
                $value   = $parameter->getDefaultValue();
                $default = ' = ' . var_export($value, TRUE);
            }

            else if ($parameter->isOptional()) {
                $default = ' = null';
            }

            $ref = '';

            if ($parameter->isPassedByReference()) {
                $ref = '&';
            }

            $parameters[] = $typeHint . $ref . $name . $default;
        }

        return implode(', ', $parameters);
    }
}