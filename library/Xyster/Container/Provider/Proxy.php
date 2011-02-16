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
use Xyster\Type\Proxy\IHandler;
/**
 * Provider to allow for runtime proxies
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Proxy extends Delegate
{
    /**
     * @var IHandler
     */
    protected $_handler;
    
    /**
     * Creates a new delegate
     * 
     * @param IProvider $delegate The delegate provider
     * @param IHandler $handler The handler to use for the proxy
     */
    public function __construct(IProvider $delegate, IHandler $handler)
    {
        parent::__construct($delegate);
        $this->_handler = $handler;
    }

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
        $builder = new \Xyster\Type\Proxy\Builder();
        return $builder->setCallParentConstructor(false)
            ->setDelegate($this->_delegate->get($container, $into))
            ->setHandler($this->_handler)
            ->create();
    }
    
    /**
     * Gets the label for the type of provider (for instance Caching or Singleton).
     * 
     * @return string The provider label
     */
    public function getLabel()
    {
        return 'Proxy';
    }
}