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
 * @see Xyster_Container_Injection_Factory_Abstract
 */
require_once 'Xyster/Container/Injection/Factory/Abstract.php';
/**
 * @see Xyster_Container_Injection_Composite
 */
require_once 'Xyster/Container/Injection/Composite.php';
/**
 * Creates constructor injection adapters 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Factory_Composite extends Xyster_Container_Injection_Factory_Abstract
{
    private $_injectionFactories = array();
    
    /**
     * Creates a new setter injection factory
     *
     * @param array $injectionFactories An array of injection factories
     */
    public function __construct( array $injectionFactories )
    {
        foreach( $injectionFactories as $v ) {
            if (! $v instanceof Xyster_Container_Injection_Factory ) {
                require_once 'Xyster/Container/Injection/Exception.php';
                throw new Xyster_Container_Injection_Exception('Arguments must be injection factories');
            }
            $this->_injectionFactories[] = $v;
        }
    }
    
    /**
     * Create a new constructor adapter
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
        $injectors = array();
        foreach( $this->_injectionFactories as $factory ) {
            /* @var $factory Xyster_Container_Injection_Factory */
            $injectors[] = $factory->createComponentAdapter($monitor,
                $properties, $key, $implementation, $parameters);
        }
        
        $useNames = Xyster_Container_Behavior_Factory_Abstract::arePropertiesPresent($properties, Xyster_Container_Features::USE_NAMES());
        return new Xyster_Container_Injection_Composite($key, $implementation,
            $parameters, $monitor, $useNames, $injectors);
    }
}