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
 * @see Xyster_Container_Behavior_Factory
 */
require_once 'Xyster/Container/Behavior/Factory.php';
/**
 * A component factory that creates property applicator instances
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_Factory_Adaptive implements Xyster_Container_Behavior_Factory
{
    /**
     * Create a new component adapter based on the specified arguments
     * 
     * {@inherit}
     * 
     * @param Xyster_Container_Component_Monitor $componentMonitor the component monitor
     * @param Zend_Config $componentProperties the component properties
     * @param mixed $componentKey the key to be associated with this adapter.
     * @param string $componentImplementation 
     * @param mixed $parameters 
     * @throws Exception if the creation of the component adapter fails
     * @return Xyster_Container_Component_Adapter The component adapter
     */
    public function createComponentAdapter(Xyster_Container_Component_Monitor $componentMonitor, Zend_Config $componentProperties, $componentKey, $componentImplementation, $parameters)
    {
        $factories = new Xyster_Collection_List;
        $lastFactory = $this->_makeInjectionFactory();
        $this->_processPropertyApplying($componentProperties, $factories);
        $this->_processAutomatic($componentProperties, $factories);
        $this->_processCaching($componentProperties, $componentImplementation, $factories);

        foreach( $factories as $componentFactory ) {
            if ($lastFactory != null && $componentFactory instanceof Xyster_Container_Behavior_Factory ) {
                /* @var $componentFactory Xyster_Container_Behavior_Factory */
                $componentFactory->wrap($lastFactory);
            }
            $lastFactory = $componentFactory;
        }

        return $lastFactory->createComponentAdapter($componentMonitor,
            $componentProperties, $componentKey, $componentImplementation, $parameters);
    }

    /**
     * Adds a component adapter
     *
     * @param Xyster_Container_Component_Monitor $componentMonitor
     * @param Zend_Config $componentProperties
     * @param Xyster_Container_Component_Adapter $adapter
     * @return Xyster_Container_Component_Adapter
     */
    public function addComponentAdapter(Xyster_Container_Component_Monitor $componentMonitor, Zend_Config $componentProperties, Xyster_Container_Component_Adapter $adapter)
    {
        $factories = new Xyster_Collection_List;
        $this->_processSynchronizing($componentProperties, $factories);
        $this->_processImplementationHiding($componentProperties, $factories);
        $this->_processCaching($componentProperties, $adapter->getImplementation(), $factories);

        $lastFactory = null;
        foreach( $factories as $componentFactory ) {
            /* @var $componentFactory Xyster_Container_Behavior_Factory */
            if ($lastFactory != null) {
                $componentFactory->wrap($lastFactory);
            }
            $lastFactory = $componentFactory;
        }

        if ($lastFactory == null) {
            return $adapter;
        }

        return $lastFactory->addComponentAdapter($componentMonitor, $componentProperties, $adapter);
    }

    /**
     * Gets the injection factory
     *
     * @return Xyster_Container_Injection_Adaptive
     */
    protected function _makeInjectionFactory()
    {
        return new Xyster_Container_Injection_Adaptive;
    }

    protected function _processCaching(Zend_Config $componentProperties, ReflectionClass $componentImplementation, Xyster_Collection_List $factories)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::CACHE()) ) {
            $factories->add(new Xyster_Container_Behavior_Factory_Cached);
        }
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::NO_CACHE());
    }

    protected function _processPropertyApplying(Zend_Config $componentProperties, Xyster_Collection_List $factories)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::PROPERTY_APPLYING())) {
            $factories->add(new Xyster_Container_Behavior_Factory_PropertyApplicator);
        }
    }

    protected function _processAutomatic(Zend_Config $componentProperties, Xyster_Collection_List $factories)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($componentProperties, Xyster_Container_Features::AUTOMATIC())) {
            $factories->add(new Xyster_Container_Behavior_Factory_Automated);
        }
    }


    public function wrap( Xyster_Container_Component_Factory $delegate )
    {
        throw new Exception();
    }
}