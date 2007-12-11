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
 * @see Xyster_Container_Injection_Method
 */
require_once 'Xyster/Container/Injection/Method.php';
/**
 * Creates constructor injection adapters 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Injection_Factory_Method implements Xyster_Container_Injection_Factory
{
    private $_injectionMethodName = 'inject';
    
    /**
     * Creates a new method injection factory
     *
     * @param string $injectionMethodName the name of the injection method
     */
    public function __construct( $injectionMethodName = 'inject')
    {
        $this->_injectionMethodName = $injectionMethodName;
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
        return new Xyster_Container_Injection_Method($key,
            $implementation, $parameters, $monitor,
            $this->_injectionMethodName);
    }
}