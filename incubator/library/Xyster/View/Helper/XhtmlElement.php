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
 * @package   Xyster_View
 * @subpackage Helpers
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * View helper for generating XHTML elements
 *
 * @category  Xyster
 * @package   Xyster_View
 * @subpackage Helpers
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
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