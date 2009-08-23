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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * An action placeholder in internal collection queues
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Collection_Action
{
    private $_added;
    private $_removed;
    private $_method;
    private $_args;
    
    /**
     * Creates a new action for putting in a queue
     * 
     * @param string $method The name of the method to call
     * @param array $args The arguments to pass to the method
     * @param mixed $added The value that is added as a result of the action
     * @param mixed $removed The value that is removed as a result of the action
     */
    public function __construct($method, array $args, $added, $removed)
    {
        $this->_method = $method;
        $this->_args = $args;
        $this->_added = $added;
        $this->_removed = $removed;
    }
    
    /**
     * Gets the added value
     * 
     * @return mixed
     */
    public function getAdded()
    {
        return $this->_added;
    }
    
    /**
     * Gets the removed value
     * 
     * @return mixed
     */
    public function getRemoved()
    {
        return $this->_removed;
    }
    
    /**
     * Performs the operation
     * 
     * @param mixed $object Either a Xyster_Collection_Interface or Map
     */
    public function operate($object)
    {
        call_user_func_array(array($object, $this->_method), $this->_args);
    }
}