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
 * @see Xyster_Container_Behavior_Factory_Abstract
 */
require_once 'Xyster/Container/Behavior/Factory/Abstract.php';
/**
 * @see Xyster_Container_Features
 */
require_once 'Xyster/Container/Features.php';
/**
 * @see Xyster_Collection_List
 */
require_once 'Xyster/Collection/List.php';
/**
 * @see Xyster_Container_Injection_Factory_Adaptive
 */
require_once 'Xyster/Container/Injection/Factory/Adaptive.php';
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
        $factories = new Xyster_Collection_List;
        $lastFactory = $this->_makeInjectionFactory();
        $this->_processPropertyApplying($properties, $factories);
        $this->_processAutomatic($properties, $factories);
        $this->_processCaching($properties, $implementation, $factories);

        foreach( $factories as $componentFactory ) {
            if ($lastFactory != null && $componentFactory instanceof Xyster_Container_Behavior_Factory ) {
                /* @var $componentFactory Xyster_Container_Behavior_Factory */
                $componentFactory->wrap($lastFactory);
            }
            $lastFactory = $componentFactory;
        }

        return $lastFactory->createComponentAdapter($monitor, $properties,
            $key, $implementation, $parameters);
    }

    /**
     * Adds a component adapter
     *
     * @param Xyster_Container_Monitor $monitor
     * @param Xyster_Collection_Map_Interface $properties
     * @param Xyster_Container_Adapter $adapter
     * @return Xyster_Container_Adapter
     */
    public function addComponentAdapter(Xyster_Container_Monitor $monitor, Xyster_Collection_Map_Interface $properties, Xyster_Container_Adapter $adapter)
    {
        $factories = new Xyster_Collection_List;
        $this->_processCaching($properties, $adapter->getImplementation(), $factories);

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

        return $lastFactory->addComponentAdapter($monitor, $properties, $adapter);
    }

    /**
     * Wraps another factory -- not implemented
     *
     * @param Xyster_Container_Adapter_Factory $delegate
     */
    public function wrap( Xyster_Container_Adapter_Factory $delegate )
    {
        throw new Exception();
    }

    /**
     * Gets the injection factory
     *
     * @return Xyster_Container_Injection_Factory_Adaptive
     */
    protected function _makeInjectionFactory()
    {
        return new Xyster_Container_Injection_Factory_Adaptive;
    }

    protected function _processCaching(Xyster_Collection_Map_Interface $properties, ReflectionClass $componentImplementation, Xyster_Collection_List $factories)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::CACHE()) ) {
            require_once 'Xyster/Container/Behavior/Factory/Cached.php';
            $factories->add(new Xyster_Container_Behavior_Factory_Cached);
        }
        Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::NO_CACHE());
    }

    protected function _processPropertyApplying(Xyster_Collection_Map_Interface $properties, Xyster_Collection_List $factories)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::PROPERTY_APPLYING())) {
            require_once 'Xyster/Container/Behavior/Factory/PropertyApplicator.php';
            $factories->add(new Xyster_Container_Behavior_Factory_PropertyApplicator);
        }
    }

    protected function _processAutomatic(Xyster_Collection_Map_Interface $properties, Xyster_Collection_List $factories)
    {
        if (Xyster_Container_Behavior_Factory_Abstract::removePropertiesIfPresent($properties, Xyster_Container_Features::AUTOMATIC())) {
            require_once 'Xyster/Container/Behavior/Factory/Automated.php';
            $factories->add(new Xyster_Container_Behavior_Factory_Automated);
        }
    }
}