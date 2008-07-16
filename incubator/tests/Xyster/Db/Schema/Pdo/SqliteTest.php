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
// Call Xyster_Db_Schema_Pdo_SqliteTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Xyster_Db_Schema_Pdo_SqliteTest::main');
}
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestCommon.php';
require_once 'Xyster/Db/Schema/Pdo/Sqlite.php';

/**
 * Test class for Xyster_Db_Schema_Pdo_Sqlite.
 */
class Xyster_Db_Schema_Pdo_SqliteTest extends Xyster_Db_Schema_TestCommon
{
    /**
     * @var    Xyster_Db_Schema_Pdo_Sqlite
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Xyster_Db_Schema_Pdo_SqliteTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Gets the configuration for the database adapter
     * 
     * @return array
     */
    public function getConfig()
    {
        return array(
            'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE
        );
    }
    
    /**
     * Gets the adapter 
     *
     * @return string
     */
    public function getDriver()
    {
        return 'Pdo_Sqlite';
    }
    
    /**
     * Tests the 'setAdapter' method
     */
    public function testSetAdapter()
    {
        $gateway = new Xyster_Db_Schema_Pdo_Sqlite;
        $this->assertNull($gateway->getAdapter());
        $gateway->setAdapter($this->_db);
        $this->assertSame($this->_db, $gateway->getAdapter());
    }
    
    /**
     * Adds a primary key to a table
     */
    public function testAddPrimaryKeyOneColumn()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->addPrimaryKey(new Xyster_Db_PrimaryKey);
    }
    
    /**
     * Adds a primary key to a table
     */
    public function testAddPrimaryKeyMultiColumn()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->addPrimaryKey(new Xyster_Db_PrimaryKey);
    }
    
    /**
     * Removes a column from a table
     */
    public function testDropColumn()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->dropColumn(new Xyster_Db_Column, new Xyster_Db_Table);
    }
        
    /**
     * Removes a primary key from a table
     */
    public function testDropPrimary()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->dropPrimary(new Xyster_Db_PrimaryKey);
    }
    
    /**
     * Renames a column
     */
    public function testRenameColumn()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->renameColumn(new Xyster_Db_Column, 'username2', new Xyster_Db_Table);
    }
    
    /**
     * Sets the default value for a column
     */
    public function testSetDefaultValue()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->setDefaultValue(new Xyster_Db_Column, null, new Xyster_Db_Table);
    }

    /**
     * Sets the nullability for a column
     */
    public function testSetNull()
    {
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->setNull(new Xyster_Db_Column, new Xyster_Db_Table);
    }
    
    /**
     * Sets the nullability for a column
     */
    public function testSetType()
    {
        require_once 'Xyster/Db/DataType.php';
        $this->setExpectedException('Xyster_Db_Schema_Exception');
        $this->object->setType(new Xyster_Db_Column, new Xyster_Db_Table, Xyster_Db_DataType::Char());
    }
}

// Call Xyster_Db_Schema_Pdo_SqliteTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Xyster_Db_Schema_Pdo_SqliteTest::main') {
    Xyster_Db_Schema_Pdo_SqliteTest::main();
}
