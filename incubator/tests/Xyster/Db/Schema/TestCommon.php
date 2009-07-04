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
 * @subpackage Xyster_Db
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Zend/Db.php';
require_once 'Xyster/Db/Table.php';

/**
 * Test class for Xyster_Db_Schema adapters
 */
abstract class Xyster_Db_Schema_TestCommon extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    
    /**
     * @var Xyster_Db_Schema_Abstract
     */
    protected $object;
    
    /**
     * Sets up the test
     */
    protected function setUp()
    {
        $this->_db = Zend_Db::factory($this->getDriver(), $this->getConfig());
        $className = 'Xyster_Db_Schema_' . $this->getDriver(); 
        try {
            $conn = $this->_db->getConnection();
            $this->object = new $className($this->_db);
        } catch (Zend_Exception $e) {
            $this->_db = null;
            echo $e;
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting Zend_Db_Adapter_Exception, got ' . get_class($e));
            $this->markTestSkipped($e->getMessage());
        }
    }
    
    /**
     * Cleans up any tables created
     */
    protected function tearDown()
    {
        $tables = $this->_db->listTables();
        $drop = array('forum', 'message_board', 'forum_user');
        foreach( $tables as $table ) {
            if ( in_array($table, $drop) ) {
                $this->_db->query('DROP TABLE ' . $this->_db->quoteIdentifier($table));
            }
        }
        
        $indexes = $this->object->getIndexes();
        $dropIndexes = array('my_example_index');
        foreach( $indexes as $index ) {
            /* @var $index Xyster_Db_Index */
            if ( in_array($index->getName(), $dropIndexes) ) {
                try {
                    $this->object->dropIndex($index);
                } catch ( Exception $thrown ) {
                }
            }
        }
        if ( $this->object->supportsSequences() ) {
            foreach( array('my_sequence', 'my_sequence_awesome', 'forum_forum_id_seq') as $name ) {
                try {
                    $this->_db->query('DROP SEQUENCE ' . $this->_db->quoteIdentifier($name));
                } catch ( Exception $thrown ) {
                }
            }
        }
    }
    
    /**
     * Gets the configuration for the database adapter
     * 
     * @return array
     */
    abstract public function getConfig();
        
    /**
     * Gets the type of adapter
     *
     * @return string
     */
    abstract public function getDriver();
    
    /**
     * Gets the options for the TableBuilder
     *
     * @return array
     */
    public function getOptions()
    {
        return array();
    }

    /**
     * Tests creating a table with an Identity column
     */
    public function testCreateTableIdentity()
    {
        $table = new Xyster_Db_Table('forum');
        foreach( $this->getOptions() as $name => $value ) {
            $table->setOption($name, $value);
        }
        $id = new Xyster_Db_Column('forum_id');
        $id->setType(Xyster_Db_DataType::Identity());
        
        $user = new Xyster_Db_Column('username');
        $user->setType(Xyster_Db_DataType::Varchar())->setLength(50);
        
        $title = new Xyster_Db_Column('title');
        $title->setLength(255)->setType(Xyster_Db_DataType::Varchar())->setNullable(false)->setUnique();
        
        $message = new Xyster_Db_Column('message');
        $message->setType(Xyster_Db_DataType::Clob());
        
        $created = new Xyster_Db_Column('created_on');
        $created->setType(Xyster_Db_DataType::Timestamp())->setDefaultValue('2008-08-01 14:16:21');
        
        $table->addColumn($id)
            ->addColumn($user)
            ->addColumn($title)
            ->addColumn($message)
            ->addColumn($created);
            
        $this->object->createTable($table);
        return $table;
    }
    
    /**
     * Tests the 'addColumn' method
     */
    public function testAddColumn()
    {
        $this->_setupTestTable();
        $describe = $this->_db->describeTable('forum');
        $this->assertArrayNotHasKey('category_id', $describe);
        
        $column = new Xyster_Db_Column('category_id');
        $column->setType(Xyster_Db_DataType::Integer());
        $table = new Xyster_Db_Table('forum');
        
        $this->object->addColumn($column, $table);
        
        $describe2 = $this->_db->describeTable('forum');
        $this->assertArrayHasKey('category_id', $describe2);
        $this->assertEquals('category_id', $describe2['category_id']['COLUMN_NAME']);
        $this->assertEquals('int', strtolower(substr($describe2['category_id']['DATA_TYPE'], 0, 3)));
    }

    /**
     * Adds a primary key to a table
     */
    public function testAddPrimaryKeyOneColumn()
    {
        $table = $this->_setupTestTableNoPk();
        $id = $table->getColumn(0);
        
        $pk = new Xyster_Db_PrimaryKey;
        $pk->addColumn($id)->setTable($table);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertFalse($describe['forum_id']['PRIMARY']);
        $this->object->addPrimaryKey($pk);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertTrue($describe2['forum_id']['PRIMARY']);
    }
    
    /**
     * Adds a primary key to a table
     */
    public function testAddPrimaryKeyMultiColumn()
    {
        $table = $this->_setupTestTableNoPk();
        $id = $table->getColumn(0);
        $user = $table->getColumn(1);
        
        $pk = new Xyster_Db_PrimaryKey;
        $pk->addColumn($id)->addColumn($user)->setTable($table);
            
        $describe = $this->_db->describeTable('forum');
        $this->assertFalse($describe['forum_id']['PRIMARY']);
        $this->assertFalse($describe['username']['PRIMARY']);
        $this->object->addPrimaryKey($pk);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertTrue($describe2['forum_id']['PRIMARY']);
        $this->assertTrue($describe2['username']['PRIMARY']);
        $this->assertEquals(1, $describe2['forum_id']['PRIMARY_POSITION']);
        $this->assertEquals(2, $describe2['username']['PRIMARY_POSITION']);
    }

    /**
     * Tests the 'addUniqueKey' method
     */
    public function testAddUniqueKey()
    {
        $indexes = $this->object->getUniqueKeys('forum');
        $found = false;
        foreach( $indexes as $index ) {
            if ( $index->getColumn(0)->getName() == 'created_on' ) {
                $found = true;
                break;
            }
        }
        if ( $found ) {
            $this->fail('The unique index was already defined');
        }
        
        $this->_setupTestTable();
        
        $table = new Xyster_Db_Table('forum');
        $col = new Xyster_Db_Column('username');
        $uk = new Xyster_Db_UniqueKey;
        $uk->setTable($table)->setName('forum_username_idx')->addColumn($col);
        
        $this->object->addUniqueKey($uk);
        
        $indexes2 = $this->object->getUniqueKeys('forum');
        $found = false;
        foreach( $indexes2 as $index ) {
            if ( $index->getColumn(0)->getName() == 'username' ) {
                $found = true;
                break;
            }
        }
        if ( !$found ) {
            $this->fail('The unique index was not found');
        }
    }

    /**
     * Tests the 'createIndex' method
     */
    public function testCreateIndex()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(1);
        
        $index = new Xyster_Db_Index;
        $index->setTable($table)->setName('my_example_index')->addColumn($col);
        
        $this->object->createIndex($index);
        $indexes = $this->object->getIndexes('forum');
        $found = false;
        foreach( $indexes as $index ) {
            if ( $index->getName() == 'my_example_index' ) {
                $found = true;
                break;
            }
        }
        if ( !$found ) {
            $this->fail('Index not found: my_example_index');
        }
    }

    /**
     * Tests the 'createSequence' method
     */
    public function testCreateSequence()
    {
        if ( !$this->object->supportsSequences() ) {
            $this->setExpectedException('Xyster_Db_Schema_Exception', 'This database does not support sequences');
            $this->object->createSequence('foobar');
        }
        
        $name = 'my_sequence';
        $sequences = $this->object->listSequences();
        $this->assertNotContains($name, $sequences);
        $this->object->createSequence($name, null, 1, 1, 1, PHP_INT_MAX);
        $sequences2 = $this->object->listSequences();
        $this->assertContains($name, $sequences2);
    }

    /**
     * Tests the 'dropColumn' method
     */
    public function testDropColumn()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(1);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertArrayHasKey('username', $describe);
        
        $this->object->dropColumn($col, $table);
        
        $describe2 = $this->_db->describeTable('forum');
        $this->assertNotEquals($describe, $describe2);
        $this->assertArrayNotHasKey('username', $describe2);
    }

    /**
     * Tests the 'addForeignKey' and 'dropForeign' method
     */
    public function testDropForeign()
    {
        if ( !$this->object->supportsForeignKeys() ) {
            $this->setExpectedException('Xyster_Db_Schema_Exception', 'This database does not support foreign keys');
            $this->object->addForeignKey(new Xyster_Db_ForeignKey);
        }
        
        $table = $this->_setupTestTable();
        
        $table2 = new Xyster_Db_Table('forum_user');
        foreach( $this->getOptions() as $name => $value ) {
            $table2->setOption($name, $value);
        }
        $fuser = new Xyster_Db_Column('forum_username');
        $fuser->setLength(50)->setNullable(false)->setType(Xyster_Db_DataType::Varchar());
        $fgen = new Xyster_Db_Column('gender');
        $fgen->setLength(1)->setType(Xyster_Db_DataType::Char());
        $fcreated = new Xyster_Db_Column('created_on');
        $fcreated->setType(Xyster_Db_DataType::Timestamp());
        $fpk = new Xyster_Db_PrimaryKey;
        $fpk->addColumn($fuser)->setName('forum_username_pkey')->setTable($table2);
        $table2->addColumn($fuser)
            ->addColumn($fgen)
            ->addColumn($fcreated)
            ->setPrimaryKey($fpk);
        $this->object->createTable($table2);
        
        $fkey = new Xyster_Db_ForeignKey;
        $fkey->setReferencedTable($table2)
            ->addReferencedColumn($fuser)
            ->setOnDelete(Xyster_Db_ReferentialAction::Cascade())
            ->setOnUpdate(Xyster_Db_ReferentialAction::NoAction())
            ->setTable($table)
            ->addColumn($table->getColumn(1))
            ->setName('forum_user_fkey');
        $this->object->addForeignKey($fkey);
        
        $keys = $this->object->getForeignKeys();
        $found = false;
        foreach( $keys as $key ) {
            if ( $key->getName() == 'forum_user_fkey' ) {
                $found = true;
                break;
            }
        }
        if ( !$found ) {
            $this->fail('The foreign key created was not found');
        }
        
        $this->object->dropForeign($fkey);
        
        $keys2 = $this->object->getForeignKeys();
        $found = false;
        foreach( $keys2 as $key ) {
            if ( $key->getName() == 'forum_user_fkey' ) {
                $found = true;
                break;
            }
        }
        if ( $found ) {
            $this->fail('The foreign key that was deleted should not still exist');
        }
    }

    /**
     * Tests the 'dropIndex' method
     */
    public function testDropIndex()
    {
        $this->_setupTestTable();
        
        $col = new Xyster_Db_Column('username');
        $table = new Xyster_Db_Table('forum');
        $index = new Xyster_Db_Index;
        $index->setTable($table)->setName('my_example_index')->addColumn($col);
        
        $this->object->createIndex($index);
        $indexes = $this->object->getIndexes('forum');
        $found = false;
        foreach( $indexes as $index ) {
            if ( $index->getName() == 'my_example_index' ) {
                $found = true;
                break;
            }
        }
        if ( !$found ) {
            $this->fail('Index not found: my_example_index');
        }
        
        $this->object->dropIndex($index);
        
        $indexes = $this->object->getIndexes('forum');
        $found = false;
        foreach( $indexes as $index ) {
            if ( $index->getName() == 'my_example_index' ) {
                $found = true;
                break;
            }
        }
        if ( $found ) {
            $this->fail('Deleted index was found: my_example_index');
        }
    }

    /**
     * Tests the 'dropPrimaryKey' method
     */
    public function testDropPrimary()
    {
        $table = $this->_setupTestTable();
        $describe = $this->_db->describeTable('forum');
        $this->assertTrue($describe['forum_id']['PRIMARY']);
        $this->assertEquals(1, $describe['forum_id']['PRIMARY_POSITION']);
        
        $pk = new Xyster_Db_PrimaryKey;
        $pk->setTable($table)->setName('forum_pkey');
        $this->object->dropPrimary($pk);
        
        $describe2 = $this->_db->describeTable('forum');
        $this->assertFalse($describe2['forum_id']['PRIMARY']);
        $this->assertEquals(null, $describe2['forum_id']['PRIMARY_POSITION']);
    }

    /**
     * Tests the 'dropSequence' method
     */
    public function testDropSequence()
    {
        if ( !$this->object->supportsSequences() ) {
            $this->setExpectedException('Xyster_Db_Schema_Exception', 'This database does not support sequences');
            $this->object->dropSequence('foobar');
        }
        
        $name = 'my_sequence';
        $sequences = $this->object->listSequences();
        $this->assertNotContains($name, $sequences);
        $this->object->createSequence($name);
        $sequences2 = $this->object->listSequences();
        $this->assertContains($name, $sequences2);
        $this->object->dropSequence($name);
        $sequences3 = $this->object->listSequences();
        $this->assertNotContains($name, $sequences3);
    }

    /**
     * Tests the 'dropTable' method
     */
    public function testDropTable()
    {
        $table = $this->_setupTestTable();
        $tables = $this->_db->listTables();
        $this->assertContains('forum', $tables);
        $this->object->dropTable($table);
        $tables2 = $this->_db->listTables();
        $this->assertNotContains('forum', $tables2);
    }
    
    /**
     * Tests the 'getForeignKeys' method
     */
    public function testGetForeignKeys()
    {
        if ( !$this->object->supportsForeignKeys() ) {
            $this->setExpectedException('Xyster_Db_Schema_Exception', 'This database does not support foreign keys');
            $this->object->getForeignKeys();
        }
        $this->assertType('array', $this->object->getForeignKeys());
    }
    
    /**
     * Tests the 'getPrimaryKey' method
     */
    public function testGetPrimaryKey()
    {
        $this->_setupTestTable();
        
        $pk = $this->object->getPrimaryKey('forum');
        self::assertNotNull($pk);
        $col = $pk->getColumn(0);
        
        if ( $pk->getName() ) { // MySQL doesn't keep names for primary keys
            $this->assertEquals('forum_pkey', $pk->getName());
        }
        $this->assertType('Xyster_Db_Table_Lazy', $pk->getTable());
        $this->assertEquals(1, $pk->getColumnSpan());
        $this->assertEquals('forum_id', $col->getName());
        $this->assertType('Xyster_Db_DataType', $col->getType());
    }
    
    /**
     * Lists all sequences
     */
    public function testListSequences()
    {
        if ( !$this->object->supportsSequences() ) {
            $this->setExpectedException('Xyster_Db_Schema_Exception', 'This database does not support sequences');
            $this->object->listSequences();
        }
        $return = $this->object->listSequences();
        $this->assertType('array', $return);
    }
    
    /**
     * Tests the 'renameColumn' method
     */
    public function testRenameColumn()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(2);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertArrayHasKey('title', $describe);
        
        $this->object->renameColumn($col, 'subject', $table);
        
        $describe2 = $this->_db->describeTable('forum');
        $this->assertArrayHasKey('subject', $describe2);
        $this->assertArrayNotHasKey('title', $describe2);
        foreach( $describe2['subject'] as $key => $value ) {
            if ( $key != 'COLUMN_NAME' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }

    /**
     * Tests the 'renameIndex' method
     */
    public function testRenameIndex()
    {
        $this->_setupTestTable();

        $col = new Xyster_Db_Column('username');
        $table = new Xyster_Db_Table('forum');
        $idx = new Xyster_Db_Index;
        $idx->setTable($table)->setName('my_example_index')->addColumn($col);
        $this->object->createIndex($idx);
        
        $found = false;
        $indexes = $this->object->getIndexes('forum');
        foreach( $indexes as $index ) {
            if ( $index->getName() == 'my_example_index' ) {
                $found = true;
                break;
            }
        }
        if ( !$found ) {
            $this->fail('Index not found: my_example_index');
        }
        
        $this->object->renameIndex($idx, 'my_renamed_example_index');
        
        $indexes = $this->object->getIndexes('forum');
        $found = false;
        foreach( $indexes as $index ) {
            if ( $index->getName() == 'my_renamed_example_index' ) {
                $found = true;
                break;
            }
        }
        if ( !$found ) {
            $this->fail('Index not found: my_renamed_example_index');
        }
        $found = false;
        foreach( $indexes as $index ) {
            if ( $index->getName() == 'my_example_index' ) {
                $found = true;
                break;
            }
        }
        if ( $found ) {
            $this->fail('Renamed index still found: my_example_index');
        }
    }

    /**
     * Tests the 'renameSequence' method
     */
    public function testRenameSequence()
    {
        if ( !$this->object->supportsSequences() ) {
            $this->setExpectedException('Xyster_Db_Schema_Exception', 'This database does not support sequences');
            $this->object->renameSequence('foobar', 'barfoo');
        }
        
        $name = 'my_sequence';
        $sequences = $this->object->listSequences();
        $this->assertNotContains($name, $sequences);
        $this->object->createSequence($name);
        $sequences2 = $this->object->listSequences();
        $this->assertContains($name, $sequences2);
        $this->object->renameSequence($name, $name.'_awesome');
        $sequences3 = $this->object->listSequences();
        $this->assertContains($name.'_awesome', $sequences3);
    }

    /**
     * Tests the 'renameTable' method
     */
    public function testRenameTable()
    {
        $table = $this->_setupTestTable();
        $tables = $this->_db->listTables();
        $this->assertContains('forum', $tables);
        $this->object->renameTable($table, 'message_board');
        $tables2 = $this->_db->listTables();
        $this->assertContains('message_board', $tables2);
        $this->assertNotContains('forum', $tables2);
    }

    /**
     * Tests the 'setDefaultValue' method
     */
    public function testSetDefaultValue()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(2);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertEquals('', $describe['title']['DEFAULT']);
        $this->object->setDefaultValue($col, 'Default title of post', $table);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertEquals('Default title of post', $describe2['title']['DEFAULT']);
        foreach( $describe2['title'] as $key => $value ) {
            if ( $key != 'DEFAULT' ) {
                $this->assertEquals($describe['title'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }

    /**
     * Tests the 'setNull' method
     */
    public function testSetNull()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(6);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertTrue($describe['board_id']['NULLABLE']);
        $this->object->setNull($col, $table, false);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertFalse($describe2['board_id']['NULLABLE']);
        foreach( $describe2['board_id'] as $key => $value ) {
            if ( $key != 'NULLABLE' ) {
                $this->assertEquals($describe['board_id'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }

    /**
     * Tests the 'setType' method
     */
    public function testSetType()
    {
        $table = $this->_setupTestTable();
        $col = $table->getColumn(3);
        
        $describe = $this->_db->describeTable('forum');
        $this->assertNotEquals('varchar', $describe['message']['DATA_TYPE']);
        $this->object->setType($col, $table, Xyster_Db_DataType::Varchar(), 255);
        $describe2 = $this->_db->describeTable('forum');
        $this->assertEquals('varchar', strtolower($describe2['message']['DATA_TYPE']));
        $this->assertEquals(255, $describe2['message']['LENGTH']);
        foreach( $describe2['message'] as $key => $value ) {
            if ( $key != 'DATA_TYPE' && $key != 'LENGTH' ) {
                $this->assertEquals($describe['message'][$key], $value, $key . ' is not the same as the original');
            }
        }
    }
    
    /**
     * Sets up a single test table
     * 
     * @return Xyster_Db_Table
     */
    protected function _setupTestTable()
    {
        $table = new Xyster_Db_Table('forum');
        foreach( $this->getOptions() as $name => $value ) {
            $table->setOption($name, $value);
        }
        $id = new Xyster_Db_Column('forum_id');
        $id->setType(Xyster_Db_DataType::Integer())->setNullable(false);
        $pk = new Xyster_Db_PrimaryKey();
        $pk->addColumn($id)->setTable($table)->setName('forum_pkey');
        
        $user = new Xyster_Db_Column('username');
        $user->setType(Xyster_Db_DataType::Varchar())->setLength(50)->setNullable(false);
        
        $title = new Xyster_Db_Column('title');
        $title->setLength(254)->setType(Xyster_Db_DataType::Varchar())->setNullable(false)->setUnique();
        
        $message = new Xyster_Db_Column('message');
        $message->setType(Xyster_Db_DataType::Clob());
        
        $created = new Xyster_Db_Column('created_on');
        $created->setType(Xyster_Db_DataType::Timestamp())->setDefaultValue('2008-08-01 14:16:21');
        
        $locked = new Xyster_Db_Column('locked');
        $locked->setType(Xyster_Db_DataType::Boolean());
        
        $boardId = new Xyster_Db_Column('board_id');
        $boardId->setType(Xyster_Db_DataType::Integer());
        
        $table->addColumn($id)->setPrimaryKey($pk)
            ->addColumn($user)
            ->addColumn($title)
            ->addColumn($message)
            ->addColumn($created)
            ->addColumn($locked)
            ->addColumn($boardId);
            
        $this->object->createTable($table);
        return $table;
    }

    /**
     * Sets up a single test table with no primary key
     * 
     * @return Xyster_Db_Table
     */
    protected function _setupTestTableNoPk()
    {
        $table = new Xyster_Db_Table('forum');
        foreach( $this->getOptions() as $name => $value ) {
            $table->setOption($name, $value);
        }
        $id = new Xyster_Db_Column('forum_id');
        $id->setType(Xyster_Db_DataType::Integer())->setNullable(false);
        
        $user = new Xyster_Db_Column('username');
        $user->setType(Xyster_Db_DataType::Varchar())->setLength(50)->setNullable(false);
        
        $title = new Xyster_Db_Column('title');
        $title->setLength(254)->setType(Xyster_Db_DataType::Varchar())->setNullable(false)->setUnique();
        
        $message = new Xyster_Db_Column('message');
        $message->setType(Xyster_Db_DataType::Clob());
        
        $created = new Xyster_Db_Column('created_on');
        $created->setType(Xyster_Db_DataType::Timestamp())->setDefaultValue('2008-08-01 14:16:21');
        $locked = new Xyster_Db_Column('locked');
        $locked->setType(Xyster_Db_DataType::Boolean());
        
        $boardId = new Xyster_Db_Column('board_id');
        $boardId->setType(Xyster_Db_DataType::Integer());
        
        $table->addColumn($id)
            ->addColumn($user)
            ->addColumn($title)
            ->addColumn($message)
            ->addColumn($created)
            ->addColumn($locked)
            ->addColumn($boardId);
            
        $this->object->createTable($table);
        return $table;
    }
}