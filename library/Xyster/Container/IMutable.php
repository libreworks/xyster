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
namespace Xyster\Container;
/**
 * Mutable container interface
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface IMutable extends IContainer
{
    /**
     * Adds a definition to the container and autowires its dependencies based on the constructor.
     *
     * @param mixed $type A \Xyster\Type\Type or the name of a class
     * @param string $name Optional. The component name.
     * @return IMutable provides a fluent interface
     */
    function autowire($type, $name = null);

    /**
     * Adds a definition to the container and autowires its dependencies.
     *
     * @param mixed $type A \Xyster\Type\Type or the name of a class
     * @param string $name Optional. The component name.
     * @param array $except Optional.  An array of property names to ignore.
     * @return IMutable provides a fluent interface
     */
    function autowireByName($type, $name = null, array $except = array());

    /**
     * Adds a definition to the container and autowires its dependencies.
     *
     * @param mixed $type A \Xyster\Type\Type or the name of a class
     * @param string $name Optional. The component name.
     * @param array $except Optional.  An array of property names to ignore.
     * @return IMutable provides a fluent interface
     */
    function autowireByType($type, $name = null, array $except = array());

    /**
     * Adds a definition to the container.
     * 
     * @param \Xyster\Container\Definition $definition The component definition
     * @return IMutable provides a fluent interface
     */
    function add(Definition $definition);

    /**
     * Adds a provider to the container.
     * 
     * @param \Xyster\Container\Provider\IProvider $provider The provider
     * @return IMutable provides a fluent interface
     */
    function addProvider(Provider\IProvider $provider);
}