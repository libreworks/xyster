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
require_once 'Xyster/Container/IDetails.php';
/**
 * Simple dependency argument
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Argument
{
    protected $_name;
    protected $_value;
    
    /**
     * Gets the argument name
     * 
     * @return string The argument name
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
	 * Gets the argument value.
	 * 
	 * If the value is a string, it will be retrieved as a component from the
	 * container.  If the container doesn't have a component with a matching
	 * name, or the value of this argument isn't a string, it will be returned
	 * as a literal value.
	 * 
	 * @param Xyster_Container_IContainer $container The container
	 * @return mixed The argument value
     */
    public function resolve(Xyster_Container_IContainer $container)
    {
        return ( !is_string($this->_value) ||
            !$container->contains($this->_value) )
            ? $this->_value : $container->get($this->_value);
    }
}