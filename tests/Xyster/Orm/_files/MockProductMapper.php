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
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * @see Xyster_Orm_Mapper_Mock
 */
require_once 'Xyster/Orm/Mapper/Mock.php';

/**
 * A mock mapper
 *
 */
class MockProductMapper extends Xyster_Orm_Mapper_Mock
{
    protected $_table = 'zfproducts';
    protected $_index = array('name' => array('productName'));
    
    public function init()
    {
        $this->_hasJoined('bugs', array('class'=>'MockBug',
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

    protected function _getData()
    {
        return array(
            array('productId' => 1, 'productName' => 'Windows'),
            array('productId' => 2, 'productName' => 'Linux'),
            array('productId' => 3, 'productName' => 'OS X'),
        );
    }
}