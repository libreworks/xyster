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
 * @see Xyster_Container_Provider_Delegate
 */
require_once 'Xyster/Container/Provider/Delegate.php';
/**
 * Abstract provider deletate class
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Provider_Caching extends Xyster_Container_Provider_Delegate
{
    protected $_reference;
    
    /**
     * Get an instance of the provided component.
     * 
     * This method will usually create a new instance each time it is called,
     * but that is not required.  For example, a provider could keep a reference
     * to the same object or store it in an external scope.
     * 
     * @param Xyster_Container_IContainer $container The container (used for dependency resolution)
     * @param Xyster_Type $into Optional. The type into which this component will be injected
     * @return mixed The component
     */
    public function get(Xyster_Container_IContainer $container, Xyster_Type $into = null)
    {
        if ( $this->_reference === null ) {
            $this->_reference = $this->_delegate->get($container, $into);
        }
        return $this->_reference;
    }
    
    /**
     * Gets the label for the type of provider (for instance Caching or Singleton).
     * 
     * @return string The provider label
     */
    public function getLabel()
    {
        return 'Caching';
    }
}