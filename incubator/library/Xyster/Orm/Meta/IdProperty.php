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
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_orm_Meta_Property
 */
require_once 'Xyster/Orm/Meta/Property.php';
/**
 * An identifier field on an entity.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) Xyster contributors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Meta_IdProperty extends Xyster_Orm_Meta_Property
{
    /**
     * @var Xyster_Orm_Id_IGenerator
     */
    protected $_idGenerator;
    
    /**
     * Creates a new IdProperty
     * 
     * @param string $name The name of this property
     * @param Xyster_Orm_Meta_IValue $value The value contained in this property
     * @param Xyster_Type_Property_Interface $wrapper The property getter/setter
     * @param Xyster_Orm_Id_IGenerator $idGenerator The identifier generator
     */
    public function __construct($name, Xyster_Orm_Meta_IValue $value, Xyster_Type_Property_Interface $wrapper, Xyster_Orm_Id_IGenerator $idGenerator)
    {
        parent::__construct($name, $value, $wrapper, false, false);
        $this->_idGenerator = $idGenerator;
    }
     
    /**
     * Gets the identifier generator
     * 
     * @return Xyster_Orm_Id_IGenerator
     */
    public function getIdGenerator()
    {
        return $this->_idGenerator;
    }
    
    /**
     * Whether the identifier is only available after the entity has been inserted
     * 
     * @return boolean
     */
    public function isIdPostInsert()
    {
        return $this->_idGenerator instanceof Xyster_Orm_Id_IPostInsertGenerator;
    }
}