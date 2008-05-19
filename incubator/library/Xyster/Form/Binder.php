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
 * @see Xyster_Data_Binder
 */
require_once 'Xyster/Data/Binder.php';
/**
 * @see Xyster_Data_Binder_Setter_Interface
 */
require_once 'Xyster/Data/Binder/Setter/Interface.php';
/**
 * A data binder for form elements
 *
 * @category  Xyster
 * @package   Xyster_Form
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Form_Binder extends Xyster_Data_Binder implements Xyster_Data_Binder_Setter_Interface 
{
    /**
     * Creates a new form binder
     *
     * @param Zend_Form $form The form to bind element values
     */
    public function __construct( Zend_Form $form )
    {
        $this->_target = $form;
        $this->_defaultSetter = $this;
    }
    
    /**
     * Binds an entity's values to a form
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function bindEntity( Xyster_Orm_Entity $entity )
    {
        $this->bind($entity->toArray());
    }
    
    /**
     * Sets the value in the target 
     *
     * @param object $target An object
     * @param string $field The name of the field to set
     * @param mixed $value The value to set
     */
    public function set($target, $field, $value)
    {
        if  (! $target instanceof Zend_Form ) {
            require_once 'Xyster/Data/Binder/Setter/Exception.php';
            throw new Xyster_Data_Binder_Setter_Exception('Only Zend_Form objects can be used with this setter');
        }
        /* @var $target Zend_Form */
        $element = $target->getElement($field);
        if ( $element instanceof Zend_Form_Element ) {
            /* @var $element Zend_Form_Element */
            $element->setValue($value);
        }
    }
}