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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Interface realizing a visitor pattern for the container
 * 
 * The visitor should visit the container, all registered component adapter
 * instances and all instantiated components.
 *  
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_Visitor
{
    /**
     * Entry point for the Visitor traversal
     * 
     * The given node is the first object, that is asked for acceptance. Only
     * objects of type Container_Interface, Component_Adapter, or Parameter are
     * valid.
     * 
     * @param mixed $node the start node of the traversal
     * @return mixed a visitor-specific value
     * @throws Exception in case of an argument of invalid type 
     */
    function traverse($node);

    /**
     * Visit a container that has to accept the visitor
     * 
     * @param Xyster_Container_Interface $container the visited container.
     */
    function visitContainer(Xyster_Container_Interface $container);
    
    /**
     * Visit a component adapter that has to accept the visitor.
     * 
     * @param Xyster_Container_Adapter $componentAdapter the visited ComponentAdapter.
     */
    function visitComponentAdapter(Xyster_Container_Adapter $componentAdapter);
    
    /**
     * Visit a that has to accept the visitor.
     * 
     * @param Xyster_Container_Parameter $parameter the visited Parameter.
     */
    function visitParameter(Xyster_Container_Parameter $parameter);
}