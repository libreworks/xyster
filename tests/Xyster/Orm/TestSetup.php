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
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Xyster_Orm_Entity
 */
require_once 'Xyster/Orm/Entity.php';

/**
 * @see Xyster_Orm_Set
 */
require_once 'Xyster/Orm/Set.php';

require_once 'Xyster/Orm/Mapper/Factory/Abstract.php';
require_once 'Xyster/Orm/Mapper/Abstract.php';

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
            array('bugId'          => '10',
            'bugDescription' => 'Gravity still works',
            'bugStatus'      => 'NEW',
            'createdOn'      => '2007-07-29',
            'updatedOn'      => '2007-07-29',
            'reportedBy'     => 'doublecompile',
            'assignedTo'     => 'rspeed',
            'verifiedBy'     => 'keefer'),
            array('bugId'          => '11',
            'bugDescription' => 'Water is wet',
            'bugStatus'      => 'VERIFIED',
            'createdOn'      => '2007-07-29',
            'updatedOn'      => '2007-07-29',
            'reportedBy'     => 'keefer',
            'assignedTo'     => 'astratton',
            'verifiedBy'     => 'rspeed'),
            array('bugId'          => '12',
            'bugDescription' => 'Everything tastes like chicken',
            'bugStatus'      => 'FIXED',
            'createdOn'      => '2007-07-29',
            'updatedOn'      => '2007-07-29',
            'reportedBy'     => 'rspeed',
            'assignedTo'     => 'keefer',
            'verifiedBy'     => 'doublecompile'),
            array('bugId'          => '13',
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
    static protected $_mockFactory;
    
    public function setUp()
    {
        parent::setUp();

        if ( !self::$_mockFactory ) {
            self::$_mockFactory = new Xyster_Orm_Mapper_FactoryMock();
            self::$_mockFactory->getEntityMeta('MockBugProduct');
            self::$_mockFactory->get('MockBug');
            self::$_mockFactory->get('MockProduct');
            self::$_mockFactory->get('MockAccount');
        }
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
}

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
            
            $mapperName = $className . 'Mapper';
            $this->_mappers[$className] = new $mapperName();
            $this->_mappers[$className]->setFactory($this);
            $this->_mappers[$className]->init();

        }
        
        return $this->_mappers[$className];
    }
}

/**
 * A mock mapper
 *
 */
abstract class Xyster_Orm_MapperMock extends Xyster_Orm_Mapper_Abstract
{
    protected $_domain = 'mock';
    protected $_name;
    
    protected $_saved = array();
    protected $_deleted = array();

    public function __construct( $name = null )
    {
        $this->_name = $name;
    }
    public function init()
    {
    }
    public function find( $criteria )
    {
    }
    public function findAll( $criteria, $sorts = null )
    {
    }
    public function get( $id )
    {
    }
    public function getAll( array $ids = null )
    {   
    }
    public function refresh( Xyster_Orm_Entity $entity )
    {
    }
    public function save( Xyster_Orm_Entity $entity )
    {
        $this->_saved[] = $entity;
    }
    public function wasDeleted( Xyster_Orm_Entity $entity )
    {
        return in_array($entity->getPrimaryKeyAsCriterion(), $this->_deleted);
    }
    public function wasSaved( Xyster_Orm_Entity $entity )
    {
        return in_array($entity, $this->_saved, true);
    }
    
    protected function _delete( Xyster_Data_Criterion $where )
    {  
        $this->_deleted[] = $where;
    }
    protected function _insert( Xyster_Orm_Entity $entity )
    {   
    }
    protected function _update( Xyster_Orm_Entity $entity )
    {
    }
}

class MockAccount extends Xyster_Orm_Entity
{
}
class MockAccountSet extends Xyster_Orm_Set
{
}
class MockAccountMapper extends Xyster_Orm_MapperMock
{
    protected $_table = 'zfaccounts';
    
    public function init()
    {
        $meta = $this->getEntityMeta();
        $meta->hasMany('reported', array('class'=>'MockBug','id'=>'reportedBy'));
        $meta->hasMany('assigned', array('class'=>'MockBug','id'=>'assignedTo'));
        $meta->hasMany('verified', array('class'=>'MockBug','id'=>'verifiedBy'));
    }
    
    /**
     * Gets the fields
     *
     * @return array
     */
    public function getFields()
    {
        return array( 'account_name' => array(
            'TABLE_NAME'       => $this->_table,
            'COLUMN_NAME'      => 'account_name',
            'COLUMN_POSITION'  => null,
            'DATA_TYPE'        => 'varchar',
            'DEFAULT'          => null,
            'NULLABLE'         => false,
            'LENGTH'           => 255,
            'PRIMARY'          => true,
            'PRIMARY_POSITION' => 1,
        ) );
    }
}

class MockBugProduct extends Xyster_Orm_Entity
{
}
class MockBugProductSet extends Xyster_Orm_Set
{
}
class MockBugProductMapper extends Xyster_Orm_MapperMock
{
    protected $_table = 'zfbugs_products';
    
    /**
     * Gets the fields
     *
     * @return array
     */
    public function getFields()
    {
        return array(
            'bug_id' => array(
            'TABLE_NAME'       => $this->_table,
            'COLUMN_NAME'      => 'bug_id',
            'COLUMN_POSITION'  => null,
            'DATA_TYPE'        => 'int',
            'DEFAULT'          => null,
            'NULLABLE'         => false,
            'LENGTH'           => 4,
            'PRIMARY'          => true,
            'PRIMARY_POSITION' => 1 ),
            'product_id' => array(
            'TABLE_NAME'       => $this->_table,
            'COLUMN_NAME'      => 'product_id',
            'COLUMN_POSITION'  => null,
            'DATA_TYPE'        => 'int',
            'DEFAULT'          => null,
            'NULLABLE'         => false,
            'LENGTH'           => 4,
            'PRIMARY'          => true,
            'PRIMARY_POSITION' => 2 ),
            'version_id' => array(
            'TABLE_NAME'       => $this->_table,
            'COLUMN_NAME'      => 'version_id',
            'COLUMN_POSITION'  => null,
            'DATA_TYPE'        => 'int',
            'DEFAULT'          => null,
            'NULLABLE'         => false,
            'LENGTH'           => 4,
            'PRIMARY'          => true,
            'PRIMARY_POSITION' => 3 )
        );
    }
}



class MockProduct extends Xyster_Orm_Entity
{
}
class MockProductSet extends Xyster_Orm_Set
{
}
class MockProductMapper extends Xyster_Orm_MapperMock
{
    protected $_table = 'zfproducts';
    protected $_index = array('name' => array('productName'));
    
    public function init()
    {
        $meta = $this->getEntityMeta();
        $meta->hasJoined('bugs', array('class'=>'MockBug',
            'table'=>'zfbugs_products'));
    }
    
    /**
     * Gets the fields
     *
     * @return array
     */
    public function getFields()
    {
        return array(
            'product_id' => array(
                'TABLE_NAME'       => $this->_table,
                'COLUMN_NAME'      => 'product_id',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'int',
                'DEFAULT'          => null,
                'NULLABLE'         => false,
                'LENGTH'           => 4,
                'PRIMARY'          => true,
                'PRIMARY_POSITION' => 1,
            ),
            'product_name' => array(
                 'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'product_name',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'varchar',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 100,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            )
        );
    }
}

class MockBug extends Xyster_Orm_Entity
{
    /**
     * Gets the capital of Nebraska.  
     * 
     * This is a method for testing Xyster_Orm_Entity_Meta's ability to get
     * all members of a class, including method names. 
     *
     * @return string
     */
    public function getCapitalOfNebraska()
    {
        return 'Lincoln';
    }
}
class MockBugSet extends Xyster_Orm_Set
{
}
class MockBugMapper extends Xyster_Orm_MapperMock 
{
    protected $_table = 'zfbugs';
    
    public function init()
    {
        $meta = $this->getEntityMeta();
        $meta->belongsTo('reporter', array('class'=>'MockAccount','id'=>'reportedBy'));
        $meta->belongsTo('assignee', array('class'=>'MockAccount','id'=>'assignedTo'));
        $meta->belongsTo('verifier', array('class'=>'MockAccount','id'=>'verifiedBy'));
        $meta->hasJoined('products', array('class'=>'MockProduct',
            'table'=>'zfbugs_products', 'left'=>'bug_id', 'right'=>'product_id'));
    }
    
    /**
     * Gets the fields
     *
     * @return array
     */
    public function getFields()
    {
        return array(
            'bug_id'          => array(
                'TABLE_NAME'       => $this->_table,
                'COLUMN_NAME'      => 'bug_id',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'int',
                'DEFAULT'          => null,
                'NULLABLE'         => false,
                'LENGTH'           => 4,
                'PRIMARY'          => true,
                'PRIMARY_POSITION' => 1,
            ),
            'bug_description' => array(
                'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'bug_description',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'varchar',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 100,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            ),
            'bug_status'      => array(
                'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'bug_status',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'varchar',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 20,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            ),
            'created_on'      => array(
                'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'created_on',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'datetime',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 16,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            ),
            'updated_on'      => array(
                'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'updated_on',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'datetime',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 16,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            ),
            'reported_by'     => array(
                'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'reported_by',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'varchar',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 100,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            ),
            'assigned_to'     => array(
                'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'assigned_to',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'varchar',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 100,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            ),
            'verified_by'     => array(
                'TABLE_NAME'      => $this->_table,
                'COLUMN_NAME'      => 'verified_by',
                'COLUMN_POSITION'  => null,
                'DATA_TYPE'        => 'varchar',
                'DEFAULT'          => null,
                'NULLABLE'         => true,
                'LENGTH'           => 100,
                'PRIMARY'          => false,
                'PRIMARY_POSITION' => null,
            ),
        );
    }
}
