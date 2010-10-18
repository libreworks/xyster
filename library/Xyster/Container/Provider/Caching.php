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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Container\Provider;
/**
 * Abstract provider deletate class
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Caching extends Delegate
{
    protected $_reference;
    
    /**
     * Get an instance of the provided component.
     * 
     * This method will usually create a new instance each time it is called,
     * but that is not required.  For example, a provider could keep a reference
     * to the same object or store it in an external scope.
     * 
     * @param \Xyster\Container\IContainer $container The container (used for dependency resolution)
     * @param \Xyster\Type\Type $into Optional. The type into which this component will be injected
     * @return mixed The component
     */
    public function get(\Xyster\Container\IContainer $container, \Xyster\Type\Type $into = null)
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