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
 * @package   UnitTests
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once "PHPUnit/Framework/TestSuite.php";

/**
 * @see Xyster_Orm_CacheMock
 */
require_once 'Xyster/Orm/CacheMock.php';

/**
 * @see Xyster_Db_TestSetup
 */
require_once 'Xyster/Db/TestSetup.php';

/**
 * @see Zend/Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see Xyster_Orm_Loader
 */
require_once 'Xyster/Orm/Loader.php';

/**
 * @see Xyster_Orm_Mapper_Factory
 */
require_once 'Xyster/Orm/Mapper/Factory.php';

/**
 * @see Xyster_Orm_Mapper
 */
require_once 'Xyster/Orm/Mapper.php';

/**
 * Test for Xyster_Orm_Mapper
 *
 */
abstract class Xyster_Orm_Mapper_TestSetup extends Xyster_Db_TestSetup
{
    /**
     * @var Xyster_Orm_Mapper_Factory
     */
    protected $_factory;
    
    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        Xyster_Orm_Mapper::dsn('Zend_Db', $this->_db);
        
        $this->_factory();
    }
    
    /**
     * Gets the mapper factory
     *
     * @return Xyster_Orm_Mapper_Factory
     */
    protected function _factory()
    {
        if ( !$this->_factory ) {
            $this->_factory = new Xyster_Orm_Mapper_Factory();
            require_once 'Xyster/Orm/Manager.php';
            $manager = new Xyster_Orm_Manager();
            $manager->setMapperFactory($this->_factory);
            
            require_once 'Zend/Cache.php';
            require_once 'Zend/Cache/Core.php';
            $cache = new Zend_Cache_Core(array('automatic_serialization'=>true));
            $cache->setBackend(new Xyster_Orm_CacheMock());
            
            Zend_Registry::set('goodRegistryKey', $cache);
            
            $this->_factory->setDefaultMetadataCache('goodRegistryKey');
            
            Xyster_Orm_Loader::addPath(dirname(dirname(__FILE__)).'/_files');
            $this->_setupClass('Bug');
        }
        return $this->_factory;
    }
    
    /**
     * Loads a class and sets up its metadata
     *
     * @param string $className
     */
    protected function _setupClass( $className )
    {
        $map = $this->_factory()->get($className);
        $map->getSet();
    }
}