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
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Service_Gravatar
 */
require_once 'Zend/Service/Gravatar.php';
/**
 * @see Xyster_View_Helper_XhtmlElement
 */
require_once 'Xyster/View/Helper/XhtmlElement.php';
/**
 * View helper for generating Gravatar images 
 *
 * @category  Xyster
 * @package   Xyster_View
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_View_Helper_Gravatar extends Xyster_View_Helper_XhtmlElement 
{
	/**
	 * Creates a gravatar XHTML image tag
	 *
	 * @param string $email Email address of the gravatar account
	 * @param array $options See: http://site.gravatar.com/site/implement#section_1_1
	 * @param array $attribs An array of attributes for the image tag
	 * @return string Image tag
	 */
	public function gravatar( $email, array $options = array(), array $attribs = array() )
    {
		$gravatar = new Zend_Service_Gravatar($email, $options);
		$uri = $gravatar->getUri();

		// Gravatar defaults to an image size of 80 x 80 pixels
		$size = isset($options['size']) ? $options['size'] : 80;

		return '<img src="' . $uri . '" width="' . $size . '" height="'
		    . $size . '"' . $this->_htmlAttribs($attribs) . ' />';
	}
}