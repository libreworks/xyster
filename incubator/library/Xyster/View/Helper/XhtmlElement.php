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
 * @package   Xyster_View
 * @subpackage Helpers
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * View helper for generating Gravatar images 
 *
 * @category  Xyster
 * @package   Xyster_View
 * @subpackage Helpers
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_View_Helper_XhtmlElement
{
    /**
     * @var Zend_View_Interface
     */
    public $view;

    /**
     * Set the view object
     *
     * @param Zend_View_Interface $view
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    
    /**
     * Converts an associative array to a string of tag attributes.
     *
     * @param array $attribs From this array, each key-value pair is
     * converted to an attribute name and value.
     * @return string The XHTML for the attributes.
     */
    protected function _htmlAttribs( array $attribs )
    {
        $xhtml = '';
        foreach ( $attribs as $key => $val) {
            $key = $this->view->escape($key);
            if (is_array($val)) {
                $val = implode(' ', $val);
            }
            $val = $this->view->escape($val);
            $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }
}