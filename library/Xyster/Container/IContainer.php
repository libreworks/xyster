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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Main container interface
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_IContainer
{
    /**
	 * Whether this container contains a component with the given name.
	 * 
	 * @param string $name The component name
	 * @return boolean
     */
    function contains($name);
    
    /**
	 * Gets the component by name.
	 * 
	 * @param string $name The component name
	 * @return object The component
     */
    function get($name);
    
    /**
     * Gets the type of component with the given name.
     * 
     * @param string $name The component name
     * @return Xyster_Type The component type
     */
    function getType($name);
}