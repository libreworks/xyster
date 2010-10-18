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
 * @subpackage Xyster_Data
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace XysterTest\Data;
use Xyster\Data\Tuple,
        Xyster\Data\Set;
/**
 * Test for Tuple
 *
 */
class TupleTest extends DataSetTest
{
    /**
     * Tests creating a tuple
     *
     */
    public function testCreate()
    {
        $groups = array('city'=>'Baltimore', 'state'=>'Maryland', 'country'=>'United States');
        $c = $this->_getNewCollectionWithRandomValues();
        
        $tuple = new Tuple($groups, $c);
        $this->assertEquals(array_keys($groups), $tuple->getNames());
        $this->assertEquals($groups, $tuple->getValues());
        $this->assertEquals($groups['city'], $tuple->getValue('city'));
    }
    
    /**
     * Tests the 'toRow' method
     *
     */
    public function testToRow()
    {
        $groups = array('city'=>'Baltimore', 'state'=>'Maryland', 'country'=>'United States');
        $c = $this->_getNewCollectionWithRandomValues();
        $count = count($c);
        
        $tuple = new Tuple($groups, $c);
        
        $fields = new \Xyster\Data\Symbol\FieldClause(\Xyster\Data\Symbol\Field::named('city'));
        $fields->add(\Xyster\Data\Symbol\Field::named('state'))
            ->add(\Xyster\Data\Symbol\Field::count('foo'));
        
        $row = array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>$count);
        $this->assertEquals($row, $tuple->toRow($fields));
    }
    
    /**
     * Tests the 'makeTuples' static method
     *
     */
    public function testMakeTuples()
    {
        $set = new Set();
        
        $fields = new \Xyster\Data\Symbol\FieldClause(\Xyster\Data\Symbol\Field::group('city'));
        $fields->add(\Xyster\Data\Symbol\Field::group('state'))
            ->add(\Xyster\Data\Symbol\Field::count('foo'));
            
        $collection = array(
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'bar'),
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'baz'),
            array('city'=>'Philadelphia', 'state'=>'Pennsylvania', 'foo'=>'lol'),
            array('city'=>'Columbia', 'state'=>'Maryland', 'foo'=>'bar'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'wtf'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'lol')
            );
            
        $having = array(\Xyster\Data\Symbol\Expression::eq('max(state)','Maryland'));
        Tuple::makeTuples($set, $collection, $fields, $having, 2, 1 );
        
        $this->assertEquals(2, count($set));
    }
    
    /**
     * Tests the 'makeTuples' static method with no grouped field
     * @expectedException \Xyster\Data\DataException
     */
    public function testMakeTuplesNoGroup()
    {
        $set = new Set();
        
        $fields = new \Xyster\Data\Symbol\FieldClause(\Xyster\Data\Symbol\Field::count('city'));
            
        $collection = array(
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'bar'),
            array('city'=>'Baltimore', 'state'=>'Maryland', 'foo'=>'baz'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'wtf'),
            array('city'=>'Frederick', 'state'=>'Maryland', 'foo'=>'lol')
            );
        $tuples = Tuple::makeTuples($set, $collection, $fields);
    }
}