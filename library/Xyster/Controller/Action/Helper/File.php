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
 * File response headers action helper
 *
 * @category  Xyster
 * @package   Xyster_Controller
 * @subpackage Helpers
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Controller_Action_Helper_File extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * The default MIME type
     *
     */
    const DEFAULT_MIME = 'application/octet-stream';

    /**
     * Sets the appropriate headers for sending a file as a response
     * 
     * This helper uses {@link Xyster_Controller_Action_Helper_Cache} to check
     * if the file has been modified since the user last received it.
     *
     * @param string $name The filename
     * @param string $mime The MIME type of the file
     * @param int $date The unix timestamp the file was last modified
     */
    public function direct( $name, $mime = self::DEFAULT_MIME, $date = null )
    {
        $this->setFileHeaders($name, $mime, $date);
    }

    /**
     * Sets the appropriate headers for sending a file as a response
     * 
     * This helper uses {@link Xyster_Controller_Action_Helper_Cache} to check
     * if the file has been modified since the user last received it.
     *
     * @param string $name The filename
     * @param string $mime The MIME type of the file
     * @param int $date The unix timestamp the file was last modified
     */
    public function setFileHeaders( $name, $mime = self::DEFAULT_MIME, $date = null )
    {
        if ( $this->getActionController()->getHelper('Cache')->direct($date) ) {
            return;
        }

        if ( !headers_sent() ) {
            ini_set('zlib.output_compression', 'Off');
        }
        
        $this->getResponse()
            ->setHeader('Accept-Ranges', 'bytes')
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $name . '"');
    }
}