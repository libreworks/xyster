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
 * A runtime representation of a Property
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Runtime_Property_Standard extends Xyster_Orm_Runtime_Property
{
    private $_lazy;
    private $_nullable;
    private $_versionable;

    /**
     * Creates a new standard property
     *
     * @param string $name
     * @param Xyster_Orm_Type_Interface $type
     * @param boolean $lazy
     * @param boolean $nullable
     * @param boolean $versionable
     */
    public function __construct( $name, Xyster_Orm_Type_Interface $type, $lazy, $nullable, $versionable )
    {
        parent::__construct($name, $type);
        $this->_lazy = $lazy;
        $this->_nullable = $nullable;
        $this->_versionable = $versionable;
    }
    
    /**
     * Whether the property is lazy loaded
     *
     * @return boolean
     */
    public function isLazy()
    {
        return $this->_lazy;
    }
    
    /**
     * Whether the property is nullable
     *
     * @return boolean
     */
    public function isNullable()
    {
        return $this->_nullable;
    }

    /**
     * Whether the property is versionable
     *
     * @return boolean
     */
    public function isVersionable()
    {
        return $this->_versionable;
    }
}