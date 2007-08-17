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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Orm_Mapper_Factory_Abstract
 */
require_once 'Xyster/Orm/Mapper/Factory/Abstract.php';
/**
 * A simple factory for creating mappers
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapper_Factory extends Xyster_Orm_Mapper_Factory_Abstract
{
    /**
     * Default cache for meta information provided by the backend
     *
     * @var Zend_Cache_Core
     */
    protected $_defaultMetadataCache;
    
    /**
     * @var array
     */
    protected $_mappers = array();

    /**
     * Gets the mapper for a given class
     * 
     * @param string $className The name of the entity class
     * @return Xyster_Orm_Mapper The mapper object
     */
    public function get( $className )
    {
        if ( !array_key_exists($className, $this->_mappers) ) {
            
            $mapperName = Xyster_Orm_Loader::loadMapperClass($className);
            $this->_mappers[$className] = new $mapperName($this->getDefaultMetadataCache());
            $this->_mappers[$className]->setFactory($this);
            $this->_mappers[$className]->init();

        }

        return $this->_mappers[$className];
    }

    /**
     * Gets the default metadata cache for information returned by getFields()
     *
     * @return Zend_Cache_Core
     */
    public function getDefaultMetadataCache()
    {
        return $this->_defaultMetadataCache;
    }

    /**
     * Sets the default metadata cache for information returned by getFields()
     *
     * If $defaultMetadataCache is null, then no metadata cache is used by
     * default.
     *
     * @param  mixed $metadataCache Either a Cache object, or a string naming a Registry key
     */
    public function setDefaultMetadataCache($metadataCache = null)
    {
        $this->_defaultMetadataCache = $this->_setupMetadataCache($metadataCache);
    }
    
    /**
     * @param mixed $metadataCache Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Xyster_Orm_Mapper_Exception
     */
    protected function _setupMetadataCache($metadataCache)
    {
        if ($metadataCache === null) {
            return null;
        }
        if (is_string($metadataCache)) {
            require_once 'Zend/Registry.php';
            $metadataCache = Zend_Registry::get($metadataCache);
        }
        if (!$metadataCache instanceof Zend_Cache_Core) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $metadataCache;
    }
}