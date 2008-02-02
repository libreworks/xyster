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
 * @see Xyster_Container_Delegating_Abstract
 */
require_once 'Xyster/Container/Delegating/Abstract.php';
/**
 * @see Xyster_Container
 */
require_once 'Xyster/Container.php';
/**
 * Immutable container populated from properties
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Properties extends Xyster_Container_Delegating_Abstract
{
	/**
	 * Creates a new container and populate adapters from properties
	 * 
	 * Map keys will be used as implementation classes, the map values will be
	 * the keys in the container.
	 *
	 * @param Xyster_Collection_Map_Interface $properties
	 */
    public function __construct( Xyster_Collection_Map_Interface $properties )
    {
    	$delegate = new Xyster_Container;
    	parent::__construct($delegate);
    	
    	foreach( $properties as $key => $value ) {
    		$delegate->addComponent($key, $value);
    	}
    }
}