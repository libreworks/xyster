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
 * @see Xyster_Container_Injector_AbstractInjector
 */
require_once 'Xyster/Container/Injector/AbstractInjector.php';
/**
 * Provides instances of the component type
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injector_Standard extends Xyster_Container_Injector_AbstractInjector
{
    /**
     * Get an instance of the provided component.
     * 
     * @param Xyster_Container_IContainer $container The container (used for dependency resolution)
     * @param Xyster_Type $into Optional. The type into which this component will be injected
     * @return mixed The component
     */
    public function get(Xyster_Container_IContainer $container, Xyster_Type $into = null)
    {
        $type = $this->getType();
        $reflectionClass = $type->getClass();
        $constructor = ($reflectionClass) ? $reflectionClass->getConstructor() : null;
        
        try {
            $parameters = $this->_getMemberArguments($container, $constructor);
            $instance = $this->_newInstance($type, $parameters);
            $this->_injectProperties($instance, $container);
            return $instance;
        } catch ( ReflectionException $e ) {
            require_once 'Xyster/Container/Injector/Exception.php';
            throw new Xyster_Container_Injector_Exception($e->getMessage());
        }
    }
}