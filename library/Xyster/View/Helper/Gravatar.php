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
 * 
 *
 * @category  Xyster
 * @package   Xyster_View
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_View_Helper_Gravatar {

	/**
	 * Creates a gravatar XHTML image tag
	 *
	 * @param string $email Email address of the gravatar account
	 * @param array $options See: http://site.gravatar.com/site/implement#section_1_1
	 * @param string $class CSS class for the tag
	 * @return string Image tag
	 */
	public function gravatar( $email, array $options = array(), $class = '' ) {
		$gravatar = new Zend_Service_Gravatar($email, $options);
		$uri = $gravatar->getUri();

		// Gravatar defaults to an image size of 80 x 80 pixels
		$size = isset($options['size']) ? $options['size'] : 80;

		return "<img src=\"{$uri}\" class=\"{$class}\" style=\"width:{$size}px;height:{$size}px;\" />"
	}

}

?>
