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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Injection_Factory
 */
require_once 'Xyster/Container/Injection/Factory.php';
/**
 * Creates instances Injectors 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Factory_Adaptive implements Xyster_Container_Injection_Factory
{
/**
     * Create a new component adapter based on the specified arguments
     * 
     * {@inherit}
     * 
     * @param Xyster_Container_Monitor $componentMonitor the component monitor
     * @param Zend_Config $componentProperties the component properties
     * @param mixed $componentKey the key to be associated with this adapter.
     * @param string $componentImplementation 
     * @param mixed $parameters 
     * @throws Exception if the creation of the component adapter fails
     * @return Xyster_Container_Adapter The component adapter
     */
    public function createComponentAdapter(Xyster_Container_Monitor $componentMonitor, Zend_Config $componentProperties, $componentKey, $componentImplementation, $parameters)
    {
        $componentAdapter = $this->_makeIfSetterInjection($componentProperties,
            $componentMonitor, $componentKey, $componentImplementation,
            $componentAdapter, $parameters);

        if ($componentAdapter != null) {
            return $componentAdapter;
        }

        $componentAdapter = $this->_makeIfMethodInjection($componentProperties,
            $componentMonitor, $componentKey, $componentImplementation,
            $componentAdapter, $parameters);

        if ($componentAdapter != null) {
            return $componentAdapter;
        }
        
        return $this->_makeDefaultInjection($componentProperties,
            $componentMonitor, $componentKey, $componentImplementation,
            $parameters);
    }
    
    /**
     * TSIA
     *
     * @param Zend_Config $componentProperties
     * @param Xyster_Container_Monitor $componentMonitor
     * @param mixed $componentKey
     * @param mixed $componentImplementation
     * @param mixed $parameters
     * @return Xyster_Container_Adapter
     */
    protected function _makeDefaultInjection(Zend_Config $componentProperties, Xyster_Container_Monitor $componentMonitor, $componentKey, $componentImplementation, $parameters)
    {
        require_once 'Xyster/Container/Features.php';
        require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::CDI());
        
        require_once 'Xyster/Container/Injection/Factory/Constructor.php';
        $injection = new Xyster_Container_Injection_Factory_Constructor;
        return $injection->createComponentAdapter($componentMonitor,
            $componentProperties, $componentKey, $componentImplementation,
            $parameters);
    }

    /**
     * TSIA
     *
     * @param Zend_Config $componentProperties
     * @param Xyster_Container_Monitor $componentMonitor
     * @param mixed $componentKey
     * @param mixed $componentImplementation
     * @param Xyster_Container_Adapter $componentAdapter
     * @param mixed $parameters
     * @return Xyster_Container_Adapter
     */
    protected function _makeIfSetterInjection(Zend_Config $componentProperties, Xyster_Container_Monitor $componentMonitor, $componentKey, $componentImplementation, Xyster_Container_Adapter $componentAdapter, $parameters)
    {
        require_once 'Xyster/Container/Features.php';
        require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
        
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::SDI())) {
            require_once 'Xyster/Container/Injection/Factory/Setter.php';
            $injection = new Xyster_Container_Injection_Factory_Setter;
            $componentAdapter = $injection->createComponentAdapter($componentMonitor,
                $componentProperties, $componentKey, $componentImplementation,
                $parameters);
        }
        
        return $componentAdapter;
    }

    /**
     * TSIA
     *
     * @param Zend_Config $componentProperties
     * @param Xyster_Container_Monitor $componentMonitor
     * @param mixed $componentKey
     * @param mixed $componentImplementation
     * @param Xyster_Container_Adapter $componentAdapter
     * @param mixed $parameters
     * @return Xyster_Container_Adapter
     */
    protected function _makeIfMethodInjection(Zend_Config $componentProperties, Xyster_Container_Monitor $componentMonitor, $componentKey, $componentImplementation, Xyster_Container_Adapter $componentAdapter, $parameters)
    {
        require_once 'Xyster/Container/Features.php';
        require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
        
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::METHOD_INJECTION())) {
            require_once 'Xyster_Container_Injection_Factory_Method.php';
            $injection = new Xyster_Container_Injection_Factory_Method;
            $componentAdapter = $injection->createComponentAdapter($componentMonitor,
                $componentProperties, $componentKey, $componentImplementation,
                $parameters);
        }
        return $componentAdapter;
    }
}