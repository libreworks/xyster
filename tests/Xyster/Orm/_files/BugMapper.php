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
 * A mock mapper
 *
 */
class BugMapper extends Xyster_Orm_Mapper_Db
{
    protected $_table = 'zfbugs';
    
    public function init()
    {
        $this->_belongsTo('reporter', array('class'=>'Account','id'=>'reportedBy','filters'=>'accountName <> null'))
            ->_belongsTo('assignee', array('class'=>'Account','id'=>'assignedTo'))
            ->_belongsTo('verifier', array('class'=>'Account','id'=>'verifiedBy'))
            ->_hasJoined('products', array('table'=>'zfbugs_products'));
    }
}