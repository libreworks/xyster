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
class ProductMapper extends Xyster_Orm_Mapper_Db
{
    protected $_table = 'zfproducts';
    protected $_index = array('name' => array('productName'));
    protected $_options = array('metadataCache'=>'goodRegistryKey');

    
    public function init()
    {
        if ( $this->_getAdapter() instanceof Zend_Db_Adapter_Pdo_Pgsql ) {
            $this->_options['sequence'] = 'zfproducts_seq';
        }
        
        $this->_hasJoined('bugs', array('table'=>'zfbugs_products'));
    }
}