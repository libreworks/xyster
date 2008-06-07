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
 * @package   Xyster_Validate
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Zend_Validate
 */
require_once 'Zend/Validate.php';
/**
 * A validator that allows for empty values
 *
 * @category  Xyster
 * @package   Xyster_Validate
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Validate extends Zend_Validate
{
    /**
     * Adds a validator to the end of the chain
     *
     * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
     * if one exists, will not be executed.
     *
     * @param  Zend_Validate_Interface $validator
     * @param  boolean                 $breakChainOnFailure
     * @return Zend_Validate Provides a fluent interface
     */
    public function addValidator(Zend_Validate_Interface $validator, $breakChainOnFailure = false, $allowEmpty = false)
    {
        $this->_validators[] = array(
            'instance' => $validator,
            'breakChainOnFailure' => (boolean) $breakChainOnFailure,
            'allowEmpty' => $allowEmpty
            );
        return $this;
    }
    
    /**
     * Returns true if and only if $value passes all validations in the chain
     *
     * Validators are run in the order in which they were added to the chain (FIFO).
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();
        $this->_errors   = array();
        $result = true;
        foreach ($this->_validators as $element) {
            if ( $element['allowEmpty'] && empty($value) ) {
                continue;
            }
            $validator = $element['instance'];
            if ($validator->isValid($value)) {
                continue;
            }
            $result = false;
            $messages = $validator->getMessages();
            $this->_messages = array_merge($this->_messages, $messages);
            $this->_errors   = array_merge($this->_errors,   array_keys($messages));
            if ($element['breakChainOnFailure']) {
                break;
            }
        }
        return $result;        
    }
}