<?php
require_once 'Xyster/Container/Visitor.php';

class Xyster_Container_Visitor_Mock implements Xyster_Container_Visitor
{
    protected $_count = array('visitContainer'=>0,'visitComponentAdapter'=>0,'visitParameter'=>0);
    
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
    public function traverse($node)
    {
    }

    /**
     * Visit a container that has to accept the visitor
     * 
     * @param Xyster_Container_Interface $container the visited container.
     */
    public function visitContainer(Xyster_Container_Interface $container)
    {
        $this->_count[__FUNCTION__]++;
    }
    
    /**
     * Visit a component adapter that has to accept the visitor.
     * 
     * @param Xyster_Container_Adapter $componentAdapter the visited ComponentAdapter.
     */
    public function visitComponentAdapter(Xyster_Container_Adapter $componentAdapter)
    {
        $this->_count[__FUNCTION__]++;
    }
    
    /**
     * Visit a that has to accept the visitor.
     * 
     * @param Xyster_Container_Parameter $parameter the visited Parameter.
     */
    public function visitParameter(Xyster_Container_Parameter $parameter)
    {
        $this->_count[__FUNCTION__]++;
    }
    
    /**
     * Gets the number of times a method was called
     *
     * @param string $methodName
     * @return int
     */
    public function getCalled( $methodName )
    {
        return $this->_count[$methodName];
    }
}