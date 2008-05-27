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
 * @see Xyster_Container_Visitor_Abstract
 */
require_once 'Xyster/Container/Visitor/Abstract.php';
/**
 * Concrete implementation of visitor which simply checks traversals
 * 
 * This can be a useful class for other Visitor implementations to extend, as it
 * provides a default implementation. 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Visitor_TraversalChecking extends Xyster_Container_Visitor_Abstract
{
    /**
     * Visit a container that has to accept the visitor
     * 
     * @param Xyster_Container_Interface $container the visited container.
     */
    public function visitContainer(Xyster_Container_Interface $container)
    {
    	$this->_checkTraversal();
    }
    
    /**
     * Visit a component adapter that has to accept the visitor.
     * 
     * @param Xyster_Container_Adapter $componentAdapter the visited ComponentAdapter.
     */
    public function visitComponentAdapter(Xyster_Container_Adapter $componentAdapter)
    {
        $this->_checkTraversal();
    }
    
    /**
     * Visit a component adapter factory that has to accept the visitor.
     * 
     * @param Xyster_Container_Adapter_Factory $componentFactory the visited factory
     */
    public function visitComponentFactory(Xyster_Container_Adapter_Factory $componentFactory)
    {
        $this->_checkTraversal();
    }
    
    /**
     * Visit a that has to accept the visitor.
     * 
     * @param Xyster_Container_Parameter $parameter the visited Parameter.
     */
    public function visitParameter(Xyster_Container_Parameter $parameter)
    {
    	$this->_checkTraversal();
    }
}