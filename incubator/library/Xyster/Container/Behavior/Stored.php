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
 * @see Xyster_Container_Behavior_Abstract
 */
require_once 'Xyster/Container/Behavior/Abstract.php';
/**
 * Stored behavior
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_Stored extends Xyster_Container_Behavior_Abstract
{
    /**
     * @var Xyster_Container_Reference 
     */
    protected $_instanceReference;

    /**
     * Creates a new stored behavior
     * 
     * @param Xyster_Container_Adapter $delegate
     * @param Xyster_Container_Reference $reference
     */
    public function __construct( Xyster_Container_Adapter $delegate, Xyster_Container_Reference $reference )
    {
        parent::__construct($delegate);
        $this->_instanceReference = $reference;
    }

    /**
     * Flushes the cache.
     * If the component instance is started is will stop and dispose it before
     * flushing the cache.
     */
    public function flush()
    {
        $instance = $this->_instanceReference->get();
        $this->_instanceReference->set(null);
    }

    /**
     * Retrieve the component instance
     * 
     * {@inherit}
     *
     * @param Xyster_Container_Interface $container the container, that is used to resolve any possible dependencies of the instance
     * @return object the component instance.
     * @throws Exception if the component could not be instantiated.
     * @throws Exception  if the component has dependencies which could not be resolved, or instantiation of the component lead to an ambigous situation within the container.
     */
    public function getInstance( Xyster_Container_Interface $container )
    {
        $instance = $this->_instanceReference->get();
        if ($instance == null) {
            $instance = parent::getInstance($container);
            $this->_instanceReference->set($instance);
        }
        return $instance;
    }
}