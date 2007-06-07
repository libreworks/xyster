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
	 * @return Xyster_Db_Translator  Provides a fluid interface
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
	 * If the table callback is set, this will not be used
	 * 
	 * @param string $table
	 * @return Xyster_Db_Translator  Provides a fluid interface
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
	 * @return Xyster_Db_Token
	 */
	public function translate( $object )
	{
		if ( $object instanceof Xyster_Data_Field ) {
			return $this->translateField($object);
		} else if ( $object instanceof Xyster_Data_Sort ) {
			return $this->translateSort($object);
		} else if ( $object instanceof Xyster_Data_Criterion ) {
			return $this->translateCriterion($object);
		}
		require 'Xyster/Db/Exception.php';
		throw new Xyster_Db_Exception('Invalid object');
	}
	/**
	 * Converts a field to SQL
	 *
	 * @param Xyster_Data_Field $tosql  The field to translate
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateField( Xyster_Data_Field $tosql )
	{
		$tableName = $this->_table;
		$rename = $tosql->getName();
		if ( $this->_renameCallback !== null ) {
			$rename = call_user_func( $this->_renameCallback, $tosql );
		}
		$column = $this->_adapter->quoteIdentifier($rename);
		if ( $tableName ) {
			$column = "$tableName.$column";
		}
		return new Xyster_Db_Token( $tosql instanceof Xyster_Data_Field_Aggregate ?
				$tosql->getFunction()->getValue().'('.$column.')' : $column );
	}
	/**
	 * Converts a sort to SQL
	 *
	 * @param Xyster_Data_Sort $tosql  The Sort to translate
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateSort( Xyster_Data_Sort $tosql )
	{
		return new Xyster_Db_Token(
		    $this->translateField($tosql->getField())->getSql() . " "
			. $tosql->getDirection() );
	}
	/**
	 * Converts a criterion to SQL
	 *
	 * @param Xyster_Data_Criterion $tosql  The Criterion to translate
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateCriterion( Xyster_Data_Criterion $tosql )
	{
		if ( $tosql instanceof Xyster_Data_Expression ) {
			return self::translateExpression($tosql);
		} else if ( $tosql instanceof Xyster_Data_Junction ) {
			return self::translateJunction($tosql);
		}
	}
	/**
	 * Converts a Junction to SQL
	 *
	 * @param Xyster_Data_Junction $tosql  The Junction to translate
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateJunction( Xyster_Data_Junction $tosql )
	{
		$criteria = array();
		foreach( $tosql->getCriteria() as $v ) {
			$loopToken = $this->translateCriterion($v);
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
	 * @return Xyster_Db_Token  The translated SQL syntax
	 */
	public function translateExpression( Xyster_Data_Expression $tosql )
	{
		$binds = array();
		$sql = $this->translateField($tosql->getLeft())->getSql();
		$sql .= ' ' . $tosql->getOperator() . ' ';
		$val = $tosql->getRight();
		if ( $val == "NULL" || $val === null ) {
			$sql .= 'NULL';
		} else if ( $val instanceof Xyster_Data_Field ) {
			$sql .= $this->translateField($val)->getSql();
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
		return new Xyster_Db_Token($sql,$binds);
	}
}