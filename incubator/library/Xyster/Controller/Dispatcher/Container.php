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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Controller_Dispatcher_Standard
 */
require_once 'Zend/Controller/Dispatcher/Standard.php';
/**
 * @see Xyster_Controller_Action_Injector
 */
require_once 'Xyster/Controller/Action/Injector.php';
/**
 * @see Xyster_Container
 */
require_once 'Xyster/Container.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * A dispatcher that creates action controllers out of a Xyster_Container
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Dispatcher_Container extends Zend_Controller_Dispatcher_Standard
{
    /**
     * @var Xyster_Container_IContainer
     */
    protected $_container;
    
    /**
     * Constructor: Set current module to default value
     *
     * @param Xyster_Container_IContainer $container
     * @param array $params
     */
    public function __construct(Xyster_Container_IContainer $container, array $params = array())
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
    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);

        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)) {
            $controller = $request->getControllerName();
            if (!$this->getParam('useDefaultControllerAlways') && !empty($controller)) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $request->getControllerName() . ')');
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
        $injector = new Xyster_Controller_Action_Injector(
            Xyster_Container::definition($className), $request,
            $this->getResponse(), $this->getParams());
        $controller = $injector->get($this->_container);
        if (!($controller instanceof Zend_Controller_Action_Interface) && 
            !($controller instanceof Zend_Controller_Action)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception(
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
     * @return Xyster_Container_IContainer
     */
    public function getContainer()
    {
        return $this->_container;
    }
}