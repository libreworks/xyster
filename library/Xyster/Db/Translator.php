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
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Db_Token
 */
require_once 'Xyster/Db/Token.php';
/**
 * Translates objects in the Xyster_Data package into SQL fragments
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Db_Translator
{
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_adapter;
	
	/**
	 * The callback for column renaming
	 *
	 * @var mixed
	 */
	protected $_renameCallback;
	
	/**
	 * A table name to prefix columns
	 * 
	 * @var mixed
	 */
	protected $_table;

	/**
	 * Creates a new translator for a given SQL connection
	 * 
	 * @param Zend_Db_Adapter_Abstract $adapter
	 */
	public function __construct( Zend_Db_Adapter_Abstract $adapter )
	{
		$this->_adapter = $adapter;
	}

	/**
	 * Sets the callback for column renaming
	 * 
	 * This can be any valid PHP callback.  It's passed the column object.
	 *
	 * @param mixed $callback
	 * @return Xyster_Db_Translator  Provides a fluent interface
	 */
	public function setRenameCallback( $callback )
	{
		if ( !is_callable($callback) ) {
		    require_once 'Xyster/Db/Exception.php';
			throw new Xyster_Db_Exception('This is not a valid callback');
		}
		$this->_renameCallback = $callback;
		return $this;
	}
	
	/**
	 * Sets a table name to prefix to columns
	 * 
	 * @param string $table
	 * @return Xyster_Db_Translator  Provides a fluent interface
	 */
	public function setTable( $table )
	{
		$this->_table = $table;
		return $this;
	}
	
	/**
	 * Translates one of the Xyster Data objects into a SQL token
	 *
	 * @param mixed $object
     * @param boolean $quote Whether to quote field names
	 * @return Xyster_Db_Token
	 */
	public function translate( $object, $quote = true )
	{
		if ( $object instanceof Xyster_Data_Field ) {
			return $this->translateField($object, $quote);
		} else if ( $object instanceof Xyster_Data_Sort ) {
			return $this->translateSort($object, $quote);
		} else if ( $object instanceof Xyster_Data_Criterion ) {
			return $this->translateCriterion($object, $quote);
		}
		require_once 'Xyster/Db/Exception.php';
		throw new Xyster_Db_Exception('Invalid object');
	}
	
	/**
	 * Converts a field to SQL
	 *
	 * @param Xyster_Data_Field $tosql  The field to translate
     * @param boolean $quote Whether to quote field names
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateField( Xyster_Data_Field $tosql, $quote = true )
	{
		$rename = $this->_getRenamedField($tosql);
		$tableName = $this->_getTableName($tosql);
		
		$field = ( $quote ) ? $this->_adapter->quoteIdentifier($rename) : $rename;
		if ( $tableName ) {
			$field = "$tableName.$field";
		}
		
		return new Xyster_Db_Token( $tosql instanceof Xyster_Data_Field_Aggregate ?
				$tosql->getFunction()->getValue().'('.$field.')' : $field );
	}
	
	/**
	 * Converts a sort to SQL
	 *
	 * @param Xyster_Data_Sort $tosql  The Sort to translate
     * @param boolean $quote Whether to quote field names
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateSort( Xyster_Data_Sort $tosql, $quote = true )
	{
		return new Xyster_Db_Token(
		    $this->translateField($tosql->getField(), $quote)->getSql() . " "
			. $tosql->getDirection() );
	}
	
	/**
	 * Converts a criterion to SQL
	 *
	 * @param Xyster_Data_Criterion $tosql  The Criterion to translate
     * @param boolean $quote Whether to quote field names
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateCriterion( Xyster_Data_Criterion $tosql, $quote = true )
	{
	    $token = null;
		if ( $tosql instanceof Xyster_Data_Expression ) {
			$token = $this->translateExpression($tosql, $quote);
		} else if ( $tosql instanceof Xyster_Data_Junction ) {
			$token = $this->translateJunction($tosql, $quote);
		}
		return $token;
	}
	
	/**
	 * Converts a Junction to SQL
	 *
	 * @param Xyster_Data_Junction $tosql  The Junction to translate
     * @param boolean $quote Whether to quote field names
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateJunction( Xyster_Data_Junction $tosql, $quote = true )
	{
		$criteria = array();
		foreach( $tosql->getCriteria() as $v ) {
			$loopToken = $this->translateCriterion($v, $quote);
			$criteria[$loopToken->getSql()] = $loopToken;
		}
		$token = new Xyster_Db_Token( "( " .
			implode( " ".$tosql->getOperator()." ", array_keys($criteria) ) . 
			" )" );
		foreach( $criteria as $v ) {
			$token->addBindValues( $v );
		}
		return $token;
	}
	
	/**
	 * Converts an expression to SQL
	 *
	 * @param Xyster_Data_Expression $tosql  The Expression to translate
	 * @param boolean $quote Whether to quote field names
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateExpression( Xyster_Data_Expression $tosql, $quote = true )
	{
		$binds = array();
		
		$sql = $this->translateField($tosql->getLeft(), $quote)->getSql() . ' ';
		$val = $tosql->getRight();
		if ( $val === null || $val == "NULL" ) {
		    $sql .= ( $tosql->getOperator() == '=' ) ? 'IS' : 'IS NOT';
		} else {
		    $sql .= $tosql->getOperator();
		}
		$sql .= ' ';
		
		if ( $val == "NULL" || $val === null ) {
			$sql .= 'NULL';
		} else if ( $val instanceof Xyster_Data_Field ) {
			$sql .= $this->translateField($val, $quote)->getSql();
		} else {
		    
			$bindName = ':P'.str_pad(dechex(crc32((string)$tosql)), 8, '0', STR_PAD_LEFT);
			
			if ( is_array($val) ) {
			    
				if ( substr($tosql->getOperator(),-7) == 'BETWEEN' ) {
					$sql .= "{$bindName}1 AND {$bindName}2";
					$binds[$bindName.'1'] = $val[0];
					$binds[$bindName.'2'] = $val[1];
				} else if ( substr($tosql->getOperator(),-2) == 'IN' ) {
					$quoted = array();
					foreach( $val as $k=>$v ) {
						$quoted[] = $bindName.$k;
						$binds[$bindName.$k] = $v;
					}
					$sql .= '('. implode(',',$quoted) . ')';
				}
				
			} else {
				$sql .= $bindName;
				$binds[$bindName] = $val;
			}
			
		}
        
        if ( $this->_adapter instanceof Zend_Db_Adapter_Mysqli ) {
            // mysqli has no support for :name style binding, we must replace
            // bind values with '?'
            $matches = array();
            
            if ( preg_match_all('/:P[a-z0-9]+/', $sql, $matches, PREG_SET_ORDER) ) {
                $newBinds = array();
                foreach( $matches as $match ) {
                    $sql = str_replace($match[0], '?', $sql);
                    $newBinds[] = $binds[$match[0]];
                }
                $binds = $newBinds;
            }
        }

		return new Xyster_Db_Token($sql, $binds);
	}
	
	/**
	 * Gets the renamed value of the field if appropriate
	 * 
	 * This can be overridden to provide a custom renaming strategy
	 *
	 * @param Xyster_Data_Field $field
	 * @return string
	 */
	protected function _getRenamedField( Xyster_Data_Field $field )
	{
	    $rename = $field->getName();
		if ( $this->_renameCallback !== null ) {
			$rename = call_user_func($this->_renameCallback, $rename);
		}
		return $rename;
	}

	/**
	 * Gets the name of the table to use to prefix columns
	 * 
	 * This can be extended to provide a custom table name
	 *
	 * @param Xyster_Data_Field $field
	 * @return string
	 */
	protected function _getTableName( Xyster_Data_Field $field )
	{
	    return $this->_table;
	}
}