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
 * @see Xyster_Orm_Mapper_Db
 */
require_once 'Xyster/Orm/Mapper/Db.php';

/**
 * The mapper for the 'Account' entity
 *
 */
class AccountMapper extends Xyster_Orm_Mapper_Db
{
    protected $_table = 'zfaccounts';
    protected $_options = array('metadataCache'=>null, 'emulateReferentialActions'=>true);
    
    public function init()
    {
        $this->_hasMany('reported', array('class'=>'Bug','id'=>'reportedBy', 'onDelete'=>Xyster_Db_ReferentialAction::SetNull()))
            ->_hasMany('assigned', array('class'=>'Bug','id'=>'assignedTo','filters'=>'( assignedTo <> null )'))
            ->_hasMany('verified', array('class'=>'Bug','id'=>'verifiedBy'));
    }
}