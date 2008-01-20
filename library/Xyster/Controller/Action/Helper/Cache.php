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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';
/**
 * Cache control action helper
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @subpackage Helpers
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Action_Helper_Cache extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * The cache-control header to send
     * 
     * This header has been tested to work successfully over SSL
     */
    const CACHE_CONTROL_HEADER = 'Cache-Control: private, must-revalidate, max-age=0, pre-check=0';

    /**
     * Checks the user's last mod date against the date supplied
     *
     * @param int $date
     * @return boolean
     */
    public function direct( $date )
    {
        return $this->checkModifiedSince($date);
    }

    /**
     * Checks the user's last mod date against the date supplied
     * 
     * All appropriate headers are sent.  In the event the page hasn't been
     * modified, the view renderer is disabled through the front controller.
     *
     * @param int $date
     * @return boolean
     */
    public function checkModifiedSince( $date )
    {
        if ( $this->_wasModifiedSince($date) ) {
            return true;
        }
        
        $this->_lastModified($date);
        return false;
    }

    /**
     * Formats a date in GMT syntax suitable for HTTP headers
     *
     * @param int $date
     * @return string
     */
    protected function _getGmDate( $date )
    {
        return gmdate('D, d M Y H:i:s', $date) . ' GMT';
    }

    /**
     * Sends cache-control, last-modified, and etag headers 
     *
     * @param int $date
     */
    protected function _lastModified($date)
    {
        $gmdate = $this->_getGmDate($date);
    
        $this->getResponse()->setRawHeader(self::CACHE_CONTROL_HEADER)
            ->setRawHeader('Last-Modified: ' . $gmdate)
            ->setHeader('Etag', md5($gmdate));
    }
    
    /**
     * Checks the date supplied against the if-modified-since header
     *
     * @param int $date
     * @return boolean
     */
    protected function _wasModifiedSince( $date )
    {
        $since = $this->getRequest()->getServer('HTTP_IF_MODIFIED_SINCE');
        
        if ( $since && $this->_getGmDate($date) == preg_replace('/;.*$/', '', $since) ) {
            // send the not modified headers
            $this->getResponse()->setHttpResponseCode(304)
                ->setRawHeader('Status: 304 Not Modified')
                ->setHeader('Content-Length', 0, true);
            // make sure the view renderer doesn't fire
            require_once 'Zend/Controller/Front.php';
            Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
            return true;
        }
        
        return false;
    } 
}