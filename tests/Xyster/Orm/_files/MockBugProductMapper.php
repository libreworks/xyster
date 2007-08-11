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
 * @see Xyster_Orm_Mapper_Mock
 */
require_once 'Xyster/Orm/Mapper/Mock.php';

/**
 * A mock mapper
 *
 */
class MockBugProductMapper extends Xyster_Orm_Mapper_Mock
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

    protected function _getData()
    {
        return array(
            array(
                'bug_id'       => 1,
                'product_id'   => 1
            ),
            array(
                'bug_id'       => 1,
                'product_id'   => 2
            ),
            array(
                'bug_id'       => 1,
                'product_id'   => 3
            ),
            array(
                'bug_id'       => 2,
                'product_id'   => 3
            ),
            array(
                'bug_id'       => 3,
                'product_id'   => 2
            ),
            array(
                'bug_id'       => 3,
                'product_id'   => 3
            ),
        );
    }
}