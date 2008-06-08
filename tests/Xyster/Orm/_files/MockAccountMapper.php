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
 * @see Xyster_Orm_Mapper_Mock
 */
require_once 'Xyster/Orm/Mapper/Mock.php';

/**
 * A mock mapper
 *
 */

class MockAccountMapper extends Xyster_Orm_Mapper_Mock
{
    protected $_table = 'zfaccounts';
    
    protected $_options = array('emulateReferentialActions' => true);
    
    public function init()
    {
        parent::init();
        
        require_once 'Xyster/Orm/Relation.php';
        $this->_hasMany('reported', array('class'=>'MockBug','id'=>'reportedBy','onUpdate'=>Xyster_Db_ReferentialAction::Cascade(),'onDelete'=>Xyster_Db_ReferentialAction::Cascade()))
            ->_hasMany('assigned', array('class'=>'MockBug','id'=>'assignedTo','filters'=>'( assignedTo <> null )','onUpdate'=>Xyster_Db_ReferentialAction::Cascade(),'onDelete'=>Xyster_Db_ReferentialAction::Cascade()))
            ->_hasMany('verified', array('class'=>'MockBug','id'=>'verifiedBy','onUpdate'=>Xyster_Db_ReferentialAction::Cascade()));
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

    protected function _getData()
    {
        return array(
            array('accountName' => 'mmouse'),
            array('accountName' => 'dduck'),
            array('accountName' => 'goofy'),
            array('accountName' => 'doublecompile'),
            array('accountName' => 'astratton'),
            array('accountName' => 'rspeed'),
            array('accountName' => 'keefer')
        );
    }
}