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
 * @see Xyster_Container_Visitor
 */
require_once 'Xyster/Container/Visitor.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * Abstract Visitor implementation
 * 
 * A generic traverse method is implemented, that accepts any object with a
 * method named "accept", that takes a {@link Xyster_Container_Visitor} as an 
 * argument and and invokes it. Additionally it provides the checkTraversal()
 * method, that throws an exception if currently no traversal is running.
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Visitor_Abstract implements Xyster_Container_Visitor
{
	/**
	 * @var boolean
	 */
	private $_traversal = false;
	
	/**
	 * Entry point for the Visitor traversal
	 * {@inherit}
	 *
	 * @param mixed $node
	 * @throws Xyster_Container_Visitor_Exception if node is invalid for traversal
	 */
	public function traverse( $node )
	{
		$this->_traversal = true;
		$type = Xyster_Type::of($node);
        $class = $type->getClass();
         
		try {
			if ( $class ) {
				$retval = $class->getMethod('accept');
				$retval->invoke($node, $this);
				return;
			}
        } catch ( ReflectionException $thrown ) {
        }
		
		$this->_traversal = false;
		
		require_once 'Xyster/Container/Visitor/Exception.php';
		throw new Xyster_Container_Visitor_Exception($type . ' is not a valid type for traversal');
	}
	
	/**
	 * Checks the traversal flag 
	 *
	 * @throws Xyster_Container_Visitor_Exception if no traversal is active
	 */
	protected function _checkTraversal()
	{
		if ( !$this->_traversal ) {
			require_once 'Xyster/Container/Visitor/Exception.php';
			throw new Xyster_Container_Visitor_Exception("Traversal for Visitor of type " . get_class($this) . " must start with the visitor's traverse method");
		}
	}
}