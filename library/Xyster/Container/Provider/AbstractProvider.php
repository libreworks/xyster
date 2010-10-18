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
 * Abstract object creation class
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class AbstractProvider implements IProvider
{
    protected $_name;
    /**
     * @var \Xyster\Type\Type
     */
    protected $_type;
    protected $_initMethod;
    protected $_constructorArguments = array();
    protected $_properties = array();
    protected $_dependsOn = array();
    
    /**
     * Creates a new provider
     * 
     * @param \Xyster\Container\Definition $def The component definintion
     */
    public function __construct(\Xyster\Container\Definition $def)
    {
        $this->_type = $def->getType();
        $this->_name = $def->getName();
        $this->_initMethod = $def->getInitMethod();
        $this->_constructorArguments = $def->getConstructorArgs();
        $this->_properties = $def->getProperties();
        $this->_dependsOn = $def->getDependsOn();
    }
    
    /**
     * Gets the name of the component.
     *
     * @return string The component name
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the type of component.
     * 
     * @return \Xyster\Type\Type The component type
     */
    public function getType()
    {
        return $this->_type;
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