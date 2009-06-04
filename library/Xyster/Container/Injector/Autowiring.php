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
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Injector_Standard
 */
require_once 'Xyster/Container/Injector/Standard.php';
/**
 * @see Xyster_Container_Autowire
 */
require_once 'Xyster/Container/Autowire.php';
/**
 * Autowires dependencies of a component as it creates it
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injector_Autowiring extends Xyster_Container_Injector_Standard
{
    /**
     * @var Xyster_Container_Autowire
     */
    protected $_autowire;
    protected $_ignore = array();
    
    /**
     * Creates a new provider
     * 
     * @param Xyster_Container_Definition $def The component definintion
     * @param Xyster_Container_Autowire $autowire Optional. The autowire mode.
     * @param array $ignore The autowire candidates (names or types) to ignore.
     */
    public function __construct(Xyster_Container_Definition $def, Xyster_Container_Autowire $autowire = null, array $ignore = array())
    {
        parent::__construct($def);
        $this->_autowire = ( $autowire === null ) ?
            Xyster_Container_Autowire::None() : $autowire;
        $this->_ignore = $ignore;
    }
    
    /**
     * Gets the label for the type of provider.
     * 
     * @return string The provider label
     */
    public function getLabel()
    {
        return 'Autowiring';
    }
    
    /**
     * Gets the member arguments
     *
     * @param Xyster_Container_IContainer $container
     * @param ReflectionMethod $member
     * @return array
     */
    protected function _getMemberArguments( Xyster_Container_IContainer $container, ReflectionMethod $member = null )
    {
        if ( $member === null || !$member->getNumberOfParameters() ) {
            return array();
        }
        $result = array();
        $types = Xyster_Type::getForParameters($member);
        if ( $this->_autowire !== Xyster_Container_Autowire::None() ) {
            /*
            @todo autowiring injection
            $names = $container->getNames($paramType);
            if ( count($names) == 1 ) {
                $instance = $container->get($names[0]);
            } else if ( count($names) > 1 && in_array($reflectionParameter->getName(), $names) ) {
                $instance = $container->get($reflectionParameter->getName());
            } else if ( $reflectionParameter->isDefaultValueAvailable() ) {
                $instance = $reflectionParameter->getDefaultValue();
            } else if ( !count($names) ) {
                require_once 'Xyster/Container/Injector/Exception.php';
                throw new Xyster_Container_Injector_Exception(
                    'Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $member->getDeclaringClass()->getName() .
                    ' : key not found in the container: ' . $argument);
            } else if ( count($names) > 1 ) {
                require_once 'Xyster/Container/Injector/Exception.php';
                throw new Xyster_Container_Injector_Exception(
                    'Cannot inject method argument ' .
                    $reflectionParameter->getName() .
                    ' into ' . $member->getDeclaringClass()->getName() .
                    ': more than one value is available in the container');
            }
             */
        }
        return $result;
    }
    
    /**
     * Injects properties into an instance
     * 
     * @param stdClass $instance
     * @param Xyster_Container_IContainer $container
     */
    protected function _injectProperties(stdClass $instance, Xyster_Container_IContainer $container)
    {
        if ( $this->_autowire !== Xyster_Container_Autowire::None() ) {
            // @todo autowire properties
        }
    }
}