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
 * @see Xyster_Container_NameBinding
 */
require_once 'Xyster/Container/NameBinding.php';
/**
 * A simple implementation of the NameBinding interface
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_NameBinding_Parameter implements Xyster_Container_NameBinding
{
    /**
     * @var int
     */
    protected $_index;
	
	/**
	 * @var ReflectionMethod
	 */
	protected $_member;
	
	/**
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Creates a new Parameter NameBinding
	 *
	 * @param ReflectionMethod $member
	 * @param int $index
	 */
	public function __construct( ReflectionMethod $member, $index )
	{
		$this->_index = $index;
		$this->_member = $member;
	}
	
	/**
	 * Gets the name of the parameter
	 *
	 * @return string
	 */
	public function getName()
	{
		if ( $this->_name === null ) {
			$names = array();
			
			foreach( $this->_member->getParameters() as $parameter ) {
				$names[] = $parameter->getName();
			}
			
			$this->_name = ( isset($names[$this->_index]) ) ?
                '' : $names[$this->_index];
		}
		
        return $this->_name;
	}
}