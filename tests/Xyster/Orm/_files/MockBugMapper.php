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
class MockBugMapper extends Xyster_Orm_Mapper_Mock 
{
    protected $_table = 'zfbugs';
    
    protected $_options = array('doNotRefreshAfterSave' => true);
    
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

    protected function _getData()
    {
        return array(
            array(
                'bugId'			 => 1,
                'bugDescription' => 'System needs electricity to run',
                'bugStatus'      => 'NEW',
                'createdOn'      => '2007-04-01',
                'updatedOn'      => '2007-04-01',
                'reportedBy'     => 'goofy',
                'assignedTo'     => 'mmouse',
                'verifiedBy'     => 'dduck'
            ),
            array(
                'bugId'			 => 2,
                'bugDescription' => 'Implement Do What I Mean function',
                'bugStatus'      => 'VERIFIED',
                'createdOn'      => '2007-04-02',
                'updatedOn'      => '2007-04-02',
                'reportedBy'     => 'goofy',
                'assignedTo'     => 'mmouse',
                'verifiedBy'     => 'dduck'
            ),
            array(
                'bugId'			 => 3,
                'bugDescription' => 'Where are my keys?',
                'bugStatus'      => 'FIXED',
                'createdOn'      => '2007-04-03',
                'updatedOn'      => '2007-04-03',
                'reportedBy'     => 'dduck',
                'assignedTo'     => 'mmouse',
                'verifiedBy'     => 'dduck'
            ),
            array(
                'bugId'			 => 4,
                'bugDescription' => 'Bug no product',
                'bugStatus'      => 'INCOMPLETE',
                'createdOn'      => '2007-04-04',
                'updatedOn'      => '2007-04-04',
                'reportedBy'     => 'mmouse',
                'assignedTo'     => 'goofy',
                'verifiedBy'     => 'dduck'
            ),
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
            'verifiedBy'     => 'astratton')
        );
    }
}