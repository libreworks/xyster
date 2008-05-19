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
 * @package   Xyster_Form
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Zend_Form_Element
 */
require_once 'Zend/Form/Element.php';
/**
 * Allows insertion of error messages into a form element
 *
 * The class extends from Zend_Form_Element so we can have access to the
 * protected $_messages property.
 * 
 * @category  Xyster
 * @package   Xyster_Form
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Form_ErrorBinder extends Zend_Form_Element
{
    /**
     * Binds a set of errors to the corresponding elements of a form
     *
     * @param Zend_Form $form
     * @param Xyster_Validate_Errors $errors
     * @return boolean True if errors exist and were bound
     */
    public static function bind( Zend_Form $form, Xyster_Validate_Errors $errors )
    {
        if ( !$errors->hasErrors() ) {
            return false;
        }
        
        $added = false;
        foreach( $errors as $error ) {
            /* @var $error Xyster_Validate_Error */
            if ( $element = $form->getElement($error->getField()) ) {
                $element->_messages[] = $error->getMessage();
                $added = true;
            }
        }
        return $added;
    }
}