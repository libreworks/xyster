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
class MockBugProductMapper extends Xyster_Orm_Mapper_Mock
{    
    protected $_options = array('testing'=>1234);
    
    protected $_lifetime = 99;
    
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
                'bugId'       => 1,
                'productId'   => 1,
                'versionId'   => 2
            ),
            array(
                'bugId'       => 1,
                'productId'   => 2,
                'versionId'   => 2
                ),
            array(
                'bugId'       => 1,
                'productId'   => 3,
                'versionId'   => 2
            ),
            array(
                'bugId'       => 2,
                'productId'   => 3,
                'versionId'   => 2
            ),
            array(
                'bugId'       => 3,
                'productId'   => 2,
                'versionId'   => 2
            ),
            array(
                'bugId'       => 3,
                'productId'   => 3,
                'versionId'   => 2
            ),
        );
    }
}