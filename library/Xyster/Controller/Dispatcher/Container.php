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
 * @package   Xyster_Controller
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Controller\Dispatcher;
/**
 * A dispatcher that creates action controllers out of a Xyster_Container.
 * 
 * Controllers are injected using autowiring by type.  Any setter method that
 * accepts a non-scalar or non-array value will be autowired (except for the
 * 'setResponse', 'setRequest', and 'setFrontController' methods).
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Container extends \Zend_Controller_Dispatcher_Standard
{
    /**
     * @var \Xyster\Container\IContainer
     */
    protected $_container;
    
    /**
     * Constructor: Set current module to default value
     *
     * @param \Xyster\Container\IContainer $container
     * @param array $params
     */
    public function __construct(\Xyster\Container\IContainer $container, array $params = array())
    {
        $this->_container = $container;
        parent::__construct($params);
    }
    
    /**
     * Dispatch to a controller/action
     *
     * By default, if a controller is not dispatchable, dispatch() will throw
     * an exception. If you wish to use the default controller instead, set the
     * param 'useDefaultControllerAlways' via {@link setParam()}.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return boolean
     * @throws Zend_Controller_Dispatcher_Exception
     */
    public function dispatch(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);

        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)) {
            $controller = $request->getControllerName();
            if (!$this->getParam('useDefaultControllerAlways') && !empty($controller)) {
                throw new \Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $request->getControllerName() . ')');
            }

            $className = $this->getDefaultControllerClass($request);
        } else {
            $className = $this->getControllerClass($request);
            if (!$className) {
                $className = $this->getDefaultControllerClass($request);
            }
        }

        /**
         * Load the controller class file
         */
        $className = $this->loadClass($className);
        
        /**
         * Instantiate controller with request, response, and invocation
         * arguments; throw exception if it's not an action controller
         */
        $injector = new \Xyster\Controller\Action\Injector(
            \Xyster\Container\Container::definition($className), $request,
            $this->getResponse(), $this->getParams());
        $controller = $injector->get($this->_container);
        if (!($controller instanceof \Zend_Controller_Action_Interface) &&
            !($controller instanceof \Zend_Controller_Action)) {
            throw new \Zend_Controller_Dispatcher_Exception(
                'Controller "' . $className . '" is not an instance of Zend_Controller_Action_Interface'
            );
        }
        
        /**
         * Retrieve the action name
         */
        $action = $this->getActionMethod($request);

		/**
         * Dispatch the method call
         */
        $request->setDispatched(true);

        // by default, buffer output
        $disableOb = $this->getParam('disableOutputBuffering');
        $obLevel   = ob_get_level();
        if (empty($disableOb)) {
            ob_start();
        }

        try {
            $controller->dispatch($action);
        } catch (Exception $e) {
            // Clean output buffer on error
            $curObLevel = ob_get_level();
            if ($curObLevel > $obLevel) {
                do {
                    ob_get_clean();
                    $curObLevel = ob_get_level();
                } while ($curObLevel > $obLevel);
            }

            throw $e;
        }

        if (empty($disableOb)) {
            $content = ob_get_clean();
            $response->appendBody($content);
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
    }
    
    /**
     * Gets the container
     *
     * @return \Xyster\Container\IContainer
     */
    public function getContainer()
    {
        return $this->_container;
    }
}