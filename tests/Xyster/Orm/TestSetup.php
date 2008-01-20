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

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Xyster_Orm_Loader
 */
require_once 'Xyster/Orm/Loader.php';

/**
 * @see Xyster_Orm_Mapper_FactoryMock
 */
require_once 'Xyster/Orm/Mapper/FactoryMock.php';

/**
 * Base Test for Xyster_Orm
 *
 */
class Xyster_Orm_TestSetup extends PHPUnit_Framework_TestCase
{
    /**
     * An array of bug values
     *
     * @var array
     */
    protected $_bugValues = array(
            array('bugId'          => 10,
            'bugDescription' => 'Gravity still works',
            'bugStatus'      => 'NEW',
            'createdOn'      => '2007-07-29',
            'updatedOn'      => '2007-07-29',
            'reportedBy'     => 'doublecompile',
            'assignedTo'     => 'rspeed',
            'verifiedBy'     => 'keefer'),
            array('bugId'          => 11,
            'bugDescription' => 'Water is wet',
            'bugStatus'      => 'VERIFIED',
            'createdOn'      => '2007-07-29',
            'updatedOn'      => '2007-07-29',
            'reportedBy'     => 'keefer',
            'assignedTo'     => 'astratton',
            'verifiedBy'     => 'rspeed'),
            array('bugId'          => 12,
            'bugDescription' => 'Everything tastes like chicken',
            'bugStatus'      => 'FIXED',
            'createdOn'      => '2007-07-29',
            'updatedOn'      => '2007-07-29',
            'reportedBy'     => 'rspeed',
            'assignedTo'     => 'keefer',
            'verifiedBy'     => 'doublecompile'),
            array('bugId'          => 13,
            'bugDescription' => 'What is the meaning of life?',
            'bugStatus'      => 'INCOMPLETE',
            'createdOn'      => '2007-07-29',
            'updatedOn'      => '2007-07-29',
            'reportedBy'     => 'astratton',
            'assignedTo'     => 'doublecompile',
            'verifiedBy'     => 'astratton'),
        );
        
    /**
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_mockFactory;

    /**
     * Sets up the test
     *
     */
    public function setUp()
    {
        $this->_mockFactory();
    }
    
    /**
     * Gets the mock mapper factory
     *
     * @return Xyster_Orm_Mapper_FactoryMock
     */
    protected function _mockFactory()
    {
        if ( !$this->_mockFactory ) {
            $this->_mockFactory = new Xyster_Orm_Mapper_FactoryMock();
            Xyster_Orm_Loader::addPath(dirname(__FILE__).'/_files');
            require_once 'Xyster/Orm/Manager.php';
            $manager = new Xyster_Orm_Manager();
            $manager->setMapperFactory($this->_mockFactory);
            $this->_setupClass('MockBug');
        }
        return $this->_mockFactory;
    }
        
    /**
     * Gets an example entity with a primary key
     *
     * @return MockBug
     */
    protected function _getMockEntity()
    {
        return new MockBug(current($this->_bugValues));
    }
    
    /**
     * Gets an example entity with no primary key
     *
     * @return MockBug
     */
    protected function _getMockEntityWithNoPk()
    {
        return new MockBug();
    }
    
    /**
     * Gets some mock entities with primary keys
     *
     * @return Xyster_Orm_Set
     */
    protected function _getMockEntities()
    {
        $set = new MockBugSet();
        foreach( $this->_bugValues as $values ) {
            $set->add(new MockBug($values));
        }
        return $set;
    }
    
    /**
     * Loads a class and sets up its metadata
     *
     * @param string $className
     */
    protected function _setupClass( $className )
    {
        $map = $this->_mockFactory()->get($className);
        $map->getSet();
    }
}