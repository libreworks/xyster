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
        $meta = $this->getEntityMeta();
        $meta->belongsTo('reporter', array('class'=>'Account','id'=>'reportedBy','filters'=>'accountName <> null'));
        $meta->belongsTo('assignee', array('class'=>'Account','id'=>'assignedTo'));
        $meta->belongsTo('verifier', array('class'=>'Account','id'=>'verifiedBy'));
        $meta->hasJoined('products', array('table'=>'zfbugs_products'));
    }
}