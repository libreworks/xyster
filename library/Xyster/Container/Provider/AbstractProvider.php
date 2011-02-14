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

    /**
     * Creates a new provider
     *
     * @param \Xyster\Type\Type $type The type of component this provider procudes
     * @param string $name
     */
    public function __construct(\Xyster\Type\Type $type, $name)
    {
        $this->_type = $type;
        $this->_name = !$name ? $this->_type->getName() : $name;
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