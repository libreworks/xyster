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
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Data_Clause_Interface
 */
require_once 'Xyster/Data/Clause/Interface.php';
/**
 * @see Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * Abstract clause of symbols
 *
 * @category  Xyster
 * @package   Xyster_Data
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Data_Clause implements Xyster_Data_Clause_Interface
{
	/**
	 * @var array
	 */
	protected $_items = array();
	
	/**
	 * @var Xyster_Type
	 */
	private $_type;
	
	/**
	 * Creates a new data clause
	 * 
	 * Extending classes MUST pass the type to this constructor for the class to
	 * work as expected.
	 *
	 * @param Xyster_Type $type The class type allowed in this clause
	 * @param Xyster_Data_Symbol $symbol A clause or symbol to add
	 */
	public function __construct( Xyster_Type $type, Xyster_Data_Symbol $symbol = null )
	{
		$this->_type = $type;
		if ( $symbol instanceof Xyster_Data_Clause ) {
			$this->merge($symbol);
		} else if ( $symbol instanceof Xyster_Data_Clause_Interface ) {
			foreach( $symbol as $v ) {
				$this->add($v);
			}
		} else if ( $symbol !== null ) {
			$this->add($symbol);
		}
	}
	
	/**
	 * Adds an item to this clause
	 *
	 * @param Xyster_Data_Symbol $symbol
	 * @return Xyster_Data_Clause provides a fluent interface
	 */
	public function add( Xyster_Data_Symbol $symbol )
	{
		if ( !$this->_type->isInstance($symbol) ) {
			require_once 'Xyster/Data/Clause/Exception.php';
			throw new Xyster_Data_Clause_Exception("This clause only supports " . $this->_type);
		}
		$this->_items[] = $symbol;
		return $this;
	}
	
    /**
     * Gets the number of entries in the clause
     *
     * @return int
     */
    public function count()
    {
        return count($this->_items);
    }
    
    /**
     * Gets the iterator for this clause
     *
     * @return Iterator
     */
    public function getIterator()
    {
        $iterator = null;
        if ( count($this->_items) ) {
            require_once 'Xyster/Collection/Iterator.php';
            $iterator = new Xyster_Collection_Iterator($this->_items);
        } else {
            $iterator = new EmptyIterator;
        }
        return $iterator;
    }
    
    /**
     * Adds the items from one clause to the end of this one
     *
     * @param Xyster_Data_Clause $clause
     * @return Xyster_Data_Clause provides a fluent interface
     */
    public function merge( Xyster_Data_Clause $clause )
    {
    	if ( !$this->_type->equals($clause->_type) ) {
    		require_once 'Xyster/Data/Clause/Exception.php';
            throw new Xyster_Data_Clause_Exception("This clause only supports " . $this->_type);
    	}
    	$this->_items = array_merge($this->_items, $clause->_items);
    	return $this;
    }
    
    /**
     * Removes an entry in the clause
     *
     * @param Xyster_Data_Symbol $symbol
     * @return boolean Whether the clause was changed
     */
    public function remove( Xyster_Data_Symbol $symbol )
    {
    	foreach( $this->_items as $k => $v ) {
    		if ( Xyster_Type::areDeeplyEqual($v, $symbol) ) {
    			unset($this->_items[$k]);
    			return true;
    		}
    	}
    	return false;
    }

    /**
     * Converts the clause into an array of its symbols
     *
     * @return array
     */
    public function toArray()
    {
        return array_values($this->_items);
    }
        
    /**
     * Gets the string representation of this object
     *
     * @magic
     * @return string
     */
    public function __toString()
    {
    	return implode(', ', $this->_items);
    }
}