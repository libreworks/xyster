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
 * @see Xyster_Orm_Runtime_Property_Standard
 */
require_once 'Xyster/Orm/Runtime/Property/Standard.php';
/**
 * A runtime representation of a Property
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Runtime_Property_Version extends Xyster_Orm_Runtime_Property_Standard
{
    /**
     * @var Xyster_Orm_Engine_VersionValue
     */
    private $_unsavedValue;
    
    /**
     * Creates a new standard property
     *
     * @param string $name
     * @param Xyster_Orm_Type_Interface $type
     * @param boolean $lazy
     * @param boolean $nullable
     * @param boolean $versionable
     * @param Xyster_Orm_Engine_VersionValue $unsavedValue
     */
    public function __construct( $name, Xyster_Orm_Type_Interface $type, $lazy, $nullable, $versionable, Xyster_Orm_Engine_VersionValue $unsavedValue )
    {
        parent::__construct($name, $type, $lazy, $nullable, $versionable);
        $this->_unsavedValue = $unsavedValue;
    }
    
    /**
     * Gets the version value
     *
     * @return Xyster_Orm_Engine_VersionValue
     */
    public function getUnsavedValue()
    {
        return $this->_unsavedValue;
    }
}