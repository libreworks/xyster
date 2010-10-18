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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Container\Provider;
/**
 * Abstract provider deletate class
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Delegate implements IProvider
{
    /**
     * @var IProvider
     */
    protected $_delegate;
    
    /**
     * Creates a new delegate
     * 
     * @param IProvider $delegate The delegate provider
     */
    public function __construct(IProvider $delegate)
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
     * @return \Xyster\Type\Type The component type
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
     * @param \Xyster\Container\IContainer $container The container
     * @throws \Xyster\Container\Exception if one or more required dependencies aren't met
     */
    public function validate(\Xyster\Container\IContainer $container)
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
        return $this->getLabel() . ':' . $this->_delegate->__toString();
    }
} 