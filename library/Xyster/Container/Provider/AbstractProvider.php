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
 * Abstract object creation class
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_AbstractProvider
{    
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
     * @return Xyster_Type The component type
     */
    public function getType()
    {
        return $this->_type;
    }
} 