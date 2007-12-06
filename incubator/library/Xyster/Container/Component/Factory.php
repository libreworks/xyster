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
 * Responsible for creating component adapters
 * 
 * The main use of the factory is to customize the default component adapter 
 * used when none is specified explicitly.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Component_Factory
{
    /**
     * Create a new component adapter based on the specified arguments
     * 
     * The $componentKey parameter should be returned from a call to
     * {@link Xyster_Container_Component_Adapter#getComponentKey()} on the
     * created adapter.
     * 
     * The $componentImplementation parameter is the implementation class to be
     * associated with this adapter. This value should be returned from a call
     * to {@link Xyster_Container_Component_Adapter#getComponentImplementation()}
     * on the created adapter. Should not be null.
     * 
     * The $parameters parameter are additional parameters to use by the
     * component adapter in constructing component instances. These may be used,
     * for example, to make decisions about the arguments passed into the
     * component constructor. These should be considered hints; they may be
     * ignored by some implementations. May be null, and may be of zero length.
     * 
     * @param Xyster_Container_Component_Monitor $componentMonitor the component monitor
     * @param Zend_Config $componentProperties the component properties
     * @param mixed $componentKey the key to be associated with this adapter.
     * @param string $componentImplementation 
     * @param mixed $parameters 
     * @throws Exception if the creation of the component adapter fails
     * @return Xyster_Container_Component_Adapter The component adapter
     */
    function createComponentAdapter(Xyster_Container_Component_Monitor $componentMonitor,
                                            Zend_Config $componentProperties,
                                            $componentKey,
                                            $componentImplementation,
                                            $parameters); 
}