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
 * Resolves autowire dependencies
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Autowire_AbstractResolver
{
    /**
	 * @var Xyster_Type
     */
    protected $_type;
    /**
     * @var Xyster_Container_IMutable
     */
    protected $_container;
    
    /**
	 * Creates a new resolver
	 * 
	 * @param Xyster_Container_IMutable $container
	 * @param Xyster_Type $type
     */
    public function __construct(Xyster_Container_IMutable $container, Xyster_Type $type)
    {
        $this->_container = $container;
        $this->_type = $type;
    }
    
    abstract function getMethodArguments();
    
    /**
     * Gets the properties a class should have injected
     * 
     * @param array $ignore
     * @return array of {@link Xyster_Type}
     */
    abstract function getProperties(array $ignore = array());
}