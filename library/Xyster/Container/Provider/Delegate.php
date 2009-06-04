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
 * @see Xyster_Container_IProvider
 */
require_once 'Xyster/Container/IProvider.php';
/**
 * Abstract provider deletate class
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Provider_Delegate implements Xyster_Container_IProvider
{
    /**
     * @var Xyster_Container_IProvider
     */
    protected $_delegate;
    
    /**
     * Creates a new delegate
     * 
     * @param Xyster_Container_IProvider $delegate The delegate provider
     */
    public function __construct(Xyster_Container_IProvider $delegate)
    {
        $this->_delegate = $delegate;
    }
    
    /**
     * Gets the name of the component.
     * 
     * @return string The component name
     */
    public function getName()
    {
        return $this->_delegate->getName();
    }
    
    /**
     * Gets the type of component.
     * 
     * @return Xyster_Type The component type
     */
    public function getType()
    {
        return $this->_delegate->getType();
    }
    
    /**
     * Verify that all dependencies for this component can be satisifed.
     * 
     * Normally, the details should verify this by checking that the associated
     * Container contains all the needed dependnecies.
     * 
     * @param Xyster_Container_IContainer $container The container
     * @throws Xyster_Container_Exception if one or more required dependencies aren't met
     */
    public function validate(Xyster_Container_IContainer $container)
    {
        $this->_delegate->validate($container);
    }
    
    /**
     * Converts the object into a string value
     * 
     * @magic
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel() . ':' . $this->_name;
    }
} 