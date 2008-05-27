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
 * @see Xyster_Container_Injection_Factory
 */
require_once 'Xyster/Container/Injection/Factory.php';
/**
 * Abstract injection factory 
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Container_Injection_Factory_Abstract implements Xyster_Container_Injection_Factory
{
    /**
     * Accepts a visitor for this ComponentFactory
     * 
     * The method is normally called by visiting a
     * {@link Xyster_Container_Interface}, that cascades the visitor also down
     * to all its Adapter Factory instances.
     *
     * @param Xyster_Container_Visitor $visitor the visitor
     */
    public function accept(Xyster_Container_Visitor $visitor)
    {
        $visitor->visitComponentFactory($this);
    }
    
    /**
     * Verification for the ComponentFactory
     *
     * @param Xyster_Container_Interface $container the container that is used for verification.
     * @throws Exception if one or more dependencies cannot be resolved.
     */
    public function verify(Xyster_Container_Interface $container)
    {
    }
}