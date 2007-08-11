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
 * @package   UnitTests
 * @subpackage Xyster_Data
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
 
/**
 * @see Xyster_Orm_Mapper_Factory_Abstract
 */
require_once 'Xyster/Orm/Mapper/Factory/Abstract.php';

/**
 * A mock mapper factory
 *
 */
class Xyster_Orm_Mapper_FactoryMock extends Xyster_Orm_Mapper_Factory_Abstract
{
    /**
     * @var array
     */
    protected $_mappers = array();

    /**
     * Gets the mapper for a given class
     * 
     * @param string $className The name of the entity class
     * @return Xyster_Orm_Mapper_Interface The mapper object
     */
    public function get( $className )
    {
        if ( !isset($this->_mappers[$className]) ) {
            
            $mapperName = Xyster_Orm_Loader::loadMapperClass($className);
            $this->_mappers[$className] = new $mapperName();
            $this->_mappers[$className]->setFactory($this);
            $this->_mappers[$className]->init();

        }
        
        return $this->_mappers[$className];
    }
}