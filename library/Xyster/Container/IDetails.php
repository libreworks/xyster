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
 * Component details interface
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_IDetails
{
    /**
     * Gets the autowiring mode.
     * 
     * This method should return {@link Xyster_Container_Autowire::None()} if 
     * no mode has been specified.
     * 
     * @return Xyster_Container_Autowire
     */
    function getAutowireMode();
    
    /**
     * Gets the constructor arguments
     * 
     * @return array
     */
    function getConstructorArgs();
    
    /**
     * Gets the initialization method.
     * 
     * This method will be invoked after initialization and must have no
     * arguments.
     * 
     * @return string
     */
    function getInitMethod();
    
    /**
	 * Gets the name of the component.
	 * 
	 * @return string The component name
     */
    function getName();
    
    /**
     * Gets the properties to be applied to the component.
     * 
     * @return array
     */
    function getProperties();
    
    /**
     * Gets the type of component.
     * 
     * @return Xyster_Type THe component type
     */
    function getType();
    
    /**
     * Verify that all dependencies for this component can be satisifed.
     * 
     * Normally, the details should verify this by checking that the associated
     * Container contains all the needed dependnecies.
     * 
     * @param Xyster_Container_IContainer $container The container
     * @throws Xyster_Container_Exception if one or more required dependencies aren't met
     */
    function validate(Xyster_Container_IContainer $container);
}