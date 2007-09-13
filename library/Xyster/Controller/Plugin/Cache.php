<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @subpackage Plugins
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Turns off the PHP anti-caching headers sent when a session is active
 */
ini_set('session.cache_limiter', 'none');
/**
 * Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';
/**
 * Cache control plugin
 * 
 * Notice: The inclusion of this class turns off PHP's
 * <code>session.cache_limiter</code> directive.  Please be aware of the
 * effects.  Resultantly, this class MUST be required/included BEFORE the user
 * session is started.
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @subpackage Plugins
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Plugin_Cache extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     */
    public function dispatchLoopShutdown()
    {
        $sentType = false;
        $sentTag = false;
        
        $response = $this->getResponse();
        foreach( $response->getHeaders() as $header ) {
            if ( !strcasecmp($header['name'], 'content-type') ) {
                $sentType = true;
            }
            if ( !strcasecmp($header['name'], 'etag') ) {
                $sentTag = true;
            }
        }
        
        if ( !$response->isRedirect() && !$sentType && !$sentTag ) {
            // these are basically the same anti-cache headers that PHP sends
            $response->setRawHeader('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, pre-check=0');
            $response->setRawHeader('Pragma: no-cache');
            $response->setRawHeader('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('-20 years')) . ' GMT');
            // also send content-length
            $response->setRawHeader('Content-Length: ' . strlen($response->getBody()));
        }
        if ( $sentTag && !$sentType ) { // last-mod responses, but not files
            $response->setRawHeader('Content-Length: ' . strlen($response->getBody()));
        }
    }
}