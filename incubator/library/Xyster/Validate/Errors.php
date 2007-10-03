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
 * @package   Xyster_Validate
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Collection_Abstract
 */
require_once 'Xyster/Collection/Abstract.php';
/**
 * @see Xyster_Validate_Error
 */
require_once 'Xyster/Validate/Error.php';
/**
 * An error notification class
 *
 * @category  Xyster
 * @package   Xyster_Validate
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Validate_Errors extends Xyster_Collection_Abstract
{
    /**
     * The fields
     *
     * @var array
     */
    protected $_fields = array();
    
	/**
	 * Creates a new notification object
	 *
	 * @param Xyster_Validate_Errors $errors
	 */
	public function __construct( Xyster_Validate_Errors $errors = null )
    {
        if ( $errors ) {
            $this->merge($errors);
        }
	}
	
	/**
	 * Adds an error to the collection
	 *
	 * @param mixed $item
	 * @return boolean
	 * @throws Zend_Validate_Exception if the item isn't an error
	 */
	public function add( $item )
	{
	    if ( ! $item instanceof Xyster_Validate_Error ) {
	        require_once 'Zend/Validate/Exception.php';
	        throw new Zend_Validate_Exception('Item must be of type Xyster_Validate_Error');
	    }
	    parent::add($item);
	    if ( !in_array($item->getField(), $this->_fields) ) {
	       $this->_fields[] = $item->getField();
	    }
	    return true;
	}
	
	/**
	 * Adds any messages in an input filter as errors to this collection
	 *
	 * @param Zend_Filter_Input $filter
	 */
	public function addFilterInputMessages( Zend_Filter_Input $filter )
	{
        foreach( $filter->getMessages() as $rule => $messages ) { 
            foreach( $messages as $message ) {
                $this->add(new Xyster_Validate_Error($message, $rule));
            }
        }   
	}
	
	/**
	 * Adds any messages in a validator as errors to this collection
	 *
	 * @param Zend_Validate_Interface $validate
	 * @param string $field The name of the field to which these messages apply
	 */
	public function addValidateMessages( Zend_Validate_Interface $validate, $field = null )
	{
	    foreach( $validate->getMessages() as $message ) {
	        $this->add(new Xyster_Validate_Error($message, $field));
	    }
	}
	
	/**
	 * Gets the first message available for the field supplied
	 *
	 * @param string $field The name of the field
	 * @return Xyster_Validate_Error or null if none found
	 */
	public function getError( $field )
	{
	    foreach( $this as $error ) {
	        /* @var $error Xyster_Validate_Error */
	        if ( $error->getField() == $field ) {
	            return $error;
	        }
	    }
	}
	
	/**
	 * Gets the fields to which the containing messages apply 
	 *
	 * @return array
	 */
	public function getFields()
	{
	    return array_values($this->_fields);
	}
	
	/**
	 * A convenience method to test for presence of errors
	 *
	 * @return boolean
	 */
	public function hasErrors()
	{
	    return !$this->isEmpty();
	}
}