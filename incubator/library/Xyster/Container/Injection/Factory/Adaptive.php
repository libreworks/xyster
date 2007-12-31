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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Injection_Factory
 */
require_once 'Xyster/Container/Injection/Factory.php';
/**
 * @see Xyster_Container_Features
 */
require_once 'Xyster/Container/Features.php';
/**
 * @see Xyster_Container_Behavior_Factory_Abstract
 */
require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
/**
 * Creates instances Injectors 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Factory_Adaptive implements Xyster_Container_Injection_Factory
{
    /**
     * Create a new component adapter based on the specified arguments
     * 
     * {@inherit}
     * 
     * @param Xyster_Container_Monitor $monitor the component monitor
     * @param Xyster_Collection_Map_Interface $properties the component properties
     * @param mixed $key the key to be associated with this adapter.
     * @param string $implementation 
     * @param mixed $parameters 
     * @throws Exception if the creation of the component adapter fails
     * @return Xyster_Container_Adapter The component adapter
     */
    public function createComponentAdapter(Xyster_Container_Monitor $monitor, Xyster_Collection_Map_Interface $properties, $key, $implementation, $parameters)
    {
        $componentAdapter = $this->_makeIfSetterInjection($properties, $monitor,
            $key, $implementation, null, $parameters);

        if ($componentAdapter != null) {
            return $componentAdapter;
        }

        $componentAdapter = $this->_makeIfMethodInjection($properties, $monitor,
            $key, $implementation, $componentAdapter, $parameters);

        if ($componentAdapter != null) {
            return $componentAdapter;
        }
        
        return $this->_makeDefaultInjection($properties, $monitor, $key,
            $implementation, $parameters);
    }
    
    /**
     * TSIA
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @param Xyster_Container_Monitor $monitor
     * @param mixed $key
     * @param mixed $implementation
     * @param mixed $parameters
     * @return Xyster_Container_Adapter
     */
    protected function _makeDefaultInjection(Xyster_Collection_Map_Interface $properties, Xyster_Container_Monitor $monitor, $key, $implementation, $parameters)
    {
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::CDI());
        
        require_once 'Xyster/Container/Injection/Factory/Constructor.php';
        $injection = new Xyster_Container_Injection_Factory_Constructor;
        return $injection->createComponentAdapter($monitor, $properties, $key,
            $implementation, $parameters);
    }

    /**
     * TSIA
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @param Xyster_Container_Monitor $monitor
     * @param mixed $key
     * @param mixed $implementation
     * @param Xyster_Container_Adapter $componentAdapter
     * @param mixed $parameters
     * @return Xyster_Container_Adapter
     */
    protected function _makeIfSetterInjection(Xyster_Collection_Map_Interface $properties, Xyster_Container_Monitor $monitor, $key, $implementation, Xyster_Container_Adapter $componentAdapter = null, $parameters)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::SDI())) {
            require_once 'Xyster/Container/Injection/Factory/Setter.php';
            $injection = new Xyster_Container_Injection_Factory_Setter;
            $componentAdapter = $injection->createComponentAdapter($monitor,
                $properties, $key, $implementation, $parameters);
        }
        
        return $componentAdapter;
    }

    /**
     * TSIA
     *
     * @param Xyster_Collection_Map_Interface $properties
     * @param Xyster_Container_Monitor $monitor
     * @param mixed $key
     * @param mixed $implementation
     * @param Xyster_Container_Adapter $componentAdapter
     * @param mixed $parameters
     * @return Xyster_Container_Adapter
     */
    protected function _makeIfMethodInjection(Xyster_Collection_Map_Interface $properties, Xyster_Container_Monitor $monitor, $key, $implementation, Xyster_Container_Adapter $componentAdapter = null, $parameters)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::METHOD_INJECTION())) {
            require_once 'Xyster_Container_Injection_Factory_Method.php';
            $injection = new Xyster_Container_Injection_Factory_Method;
            $componentAdapter = $injection->createComponentAdapter($monitor,
                $properties, $key, $implementation, $parameters);
        }
        return $componentAdapter;
    }
}