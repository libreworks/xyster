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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Runtime_Property
 */
require_once 'Xyster/Orm/Runtime/Property.php';
/**
 * A runtime representation of an identifier Property
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Runtime_Property_Identifier extends Xyster_Orm_Runtime_Property
{
    /**
     * @var boolean
     */
    private $_identifierByInsert = false;
    
    /**
     * @var Xyster_Orm_Engine_IdGenerator_Interface
     */
    private $_identifierGenerator;
    
    /**
     * @var boolean
     */
    private $_hasIdentifierMapper = false;
    
    /**
     * @var Xyster_Orm_Engine_IdentifierValue
     */
    private $_unsavedValue;

    /**
     * Creates a new identifier Property
     *
     * @param string $name
     * @param Xyster_Orm_Type_Interface $type
     * @param boolean $hasIdentifierMapper
     * @param Xyster_Orm_Engine_IdentifierValue $unsavedValue
     * @param unknown_type $identifierGenerator
     */
    public function __construct( $name, Xyster_Orm_Type_Interface $type, $hasIdentifierMapper, Xyster_Orm_Engine_IdentifierValue $unsavedValue, Xyster_Orm_Engine_IdGenerator_Interface $identifierGenerator = null )
    {
        parent::__construct($name, $type);
        $this->_hasIdentifierMapper = $hasIdentifierMapper;
        $this->_unsavedValue = $unsavedValue;
        $this->_identifierGenerator = $identifierGenerator;
        // @todo isAssignedById
    }
    
    /**
     * Gets the identifier generator
     *
     * @return Xyster_Orm_Engine_IdGenerator_Interface
     */
    public function getIdentifierGenerator()
    {
        return $this->_identifierGenerator;
    }
    
    /**
     * Whether this property has an identifier mapper
     *
     * @return boolean
     */
    public function hasMapper()
    {
        return $this->_hasIdentifierMapper;
    }
    
    /**
     * Whether this property has an identifier assigned by insert
     *
     * @return boolean
     */
    public function isAssignedByInsert()
    {
        return $this->_identifierByInsert;
    }
    
    /**
     * Gets the unsaved value
     *
     * @return Xyster_Orm_Engine_IdentifierValue
     */
    public function getUnsavedValue()
    {
        return $this->_unsavedValue;
    }
}