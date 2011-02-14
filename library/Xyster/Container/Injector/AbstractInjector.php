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
namespace Xyster\Container\Injector;
/**
 * Provides instances of the component type
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class AbstractInjector extends \Xyster\Container\Provider\AbstractProvider
{
    /**
     * Creates a new AbstractInjector
     *
     * @param \Xyster\Type\Type $type The component type
     * @param string $name The component name
     */
    public function __construct(\Xyster\Type\Type $type, $name)
    {
        parent::__construct($type, $name);
        $this->_checkConcrete();
    }
    
    /**
     * Checks to make sure the current implementation is a concrete class
     *
     * @throws Exception if the implementation isn't concrete
     */
    protected function _checkConcrete()
    {
        $class = $this->getType()->getClass();
        if ( $class instanceof \ReflectionClass && ( $class->isInterface() || $class->isAbstract() ) ) {
            throw new Exception($class->getName() . ' is not a concrete class');
        }
    }
    
    /**
     * Instantiate an object with given parameters
     * 
     * @param \Xyster\Type\Type $type the class to construct
     * @param \Xyster\Container\IContainer the container
     * @param \Xyster\Type\Type $into The type into which this one is being injected
     * @return object the new object
     */
    protected function _newInstance(\Xyster\Type\Type $type, \Xyster\Container\IContainer $container, array $constructorArguments = array(), \Xyster\Type\Type $into = null)
    {
        $class = $type->getClass();
        $constructor = $class ? $class->getConstructor() : null;
        return $constructor ?
            $class->newInstanceArgs(InjectionHelper::getMemberArguments($container, $type, $constructorArguments, $into)) :
            $class->newInstance();
    }
}