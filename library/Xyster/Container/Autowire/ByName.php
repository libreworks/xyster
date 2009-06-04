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
class Xyster_Container_Autowire_ByNameResolver extends Xyster_Container_Autowire_AbstractResolver
{
    public function getProperties(array $ignore = array())
    {
        $props = array();
        foreach( $this->_type->getClass()->getMethods() as $k => $method ) {
            /* @var $method ReflectionMethod */
            if ( $method->getNumberOfParameters() == 1 &&
                substr($method->getName(), 0, 3) == 'set' ) {
                $types = Xyster_Type::getForParameters($method);
                $props[] = $types[0];
            }  
        }
        return $props;
    }
}