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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Xyster_Db_Translator
 */
require_once 'Xyster/Db/Translator.php';
/**
 * @see Xyster_Orm_Relation
 */
require_Once 'Xyster/Orm/Relation.php';
/**
 * @see Xyster_Orm_Query_Parser
 */
require_once 'Xyster/Orm/Query/Parser.php';
/**
 * An exception for Xyster_Orm_Backend_Sql
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Backend_Sql_Translator extends Xyster_Db_Translator
{
	protected $_aliases = array();
	protected $_tables = array();
	protected $_class;
	protected $_main;

	/**
	 * Creates a new orm sql translator
	 * {@inherit}
	 *
	 * @param Zend_Db_Adapter_Abstract $db
	 * @param string $className
	 */
	public function __construct( Zend_Db_Adapter_Abstract $db, $className )
	{
	    parent::__construct($db);
	    
	    require_once 'Xyster/Orm/Loader.php';
	    Xyster_Orm_Loader::loadEntityClass($className);
	    $this->_class = $className;
	    $this->aliasField(current((array) Xyster_Orm_Mapper::factory($className)->getPrimary()));
	}
	
	/**
	 * Gets the main alias
	 * 
	 * @return string
	 */
	public function getMain()
	{
		return $this->_main;
	}

	/**
	 * Returns an alias to prefix a column in a SQL query
	 *
	 * @param string $column  The column to alias
	 * @throws Xyster_Orm_Backend_Sql_Exception if $column is runtime
	 * @return string
	 */
	public function aliasField( $column )
	{
		if ( Xyster_Orm_Query_Parser::isRuntime(Xyster_Data_Field::named($field), $this->_class) ) {
			require_once 'Xyster/Orm/Backend/Sql/Exception.php';
			throw new Xyster_Orm_Backend_Sql_Exception('ORM_RUNTIME_ALIAS');
		}

		$prefix = "";
		$className = $this->_class;

		$calls = Xyster_String::smartSplit('->',$field);
		if ( count($calls) > 1 ) {
			$prefixes = array();
			foreach( $calls as $call ) {
			    if ( Xyster_Orm_Relation::isValid($className, $call) ) {
    				$prefixes[] = $call;
				    $className = Xyster_Orm_Relation::get($className, $call)->getTo();
			    }
			}
			$prefix = implode('->',$prefixes).'->';
		}

		if ( !in_array($prefix, array_keys($this->_tables)) ) {
			$tableName = Xyster_Orm_Mapper::factory($className)->getTable();
			$num = 1;
			foreach( $this->_tables as $v ) { 
				if ( $v == $tableName ) {
					$num++;
				}
			}
			$this->_tables[$prefix] = $tableName;
			$this->_aliases[$prefix] = preg_replace( '/[`\]\s]*/i', '',
				$tableName) . $num;
		}

		if ( $prefix == '' && !$this->_main ) {
			$this->_main = $this->_aliases[$prefix];
		}

		return $this->_aliases[$prefix];
	}
	/**
	 * Translates a field
	 * 
	 * {@inherit}
	 * 
	 * @param Xyster_Data_Field $tosql
	 * @return Xyster_Db_Token
	 */
	public function translateField( Xyster_Data_Field $tosql )
	{
		$prefixes = Xyster_String::smartSplit('->',$tosql->getName());
		$className = $this->_class;
		if ( count($prefixes) > 1 ) {
			foreach( $prefixes as $call ) {
				if ( Xyster_Orm_Relation::isValid($className, $call) ) {
					$className = Xyster_Orm_Relation::get($className, $call)->getTo();
				}
			}
		}
		$tableName = $this->aliasField($tosql->getName());
		$rename = Xyster_Orm_Mapper::factory($className)
			->untranslateField($prefixes[count($prefixes)-1]);
		$field = $tableName.".".$this->_adapter->quoteIdentifier($rename);

		return new Xyster_Db_Token( $tosql instanceof Xyster_Data_Field_Aggregate ?
			$tosql->getAggregate()->getValue().'('.$field.')' : $field );
	}
	/**
	 * Returns a from clause for a SQL query
	 * 
	 * @return array An array of {@link Xyster_Db_Token} objects with table alias as key
	 */
	public function getFromClause()
	{
		$joined = array();
		$from = array();

		foreach( array_keys($this->_aliases) as $prefix ) {
			if ( $prefix == "" ) {
				continue;
			}

			$prefixes = explode('->',$prefix);
			$container = $this->_class;
			$prefixsofar = '';
			$lastTable = $this->_tables[''];
			$lastAlias = $this->_main;

			foreach( $prefixes as $v ) {
				if ( !$v ) {
					continue;
				}

				$prefixsofar .= $v.'->';
				$details = Xyster_Orm_Relation::get($container, $v);
				$class = $details->getTo();
				$toMap = Xyster_Orm_Mapper::factory($class);
				$fromMap = Xyster_Orm_Mapper::factory($container);
				$alias = $this->aliasField($prefixsofar.'id');

				$binds = array();
			    $joinTableSql = $toMap->getTable() . ' AS ' . $alias;
			    $localFrom = '';
				
				if (!in_array($prefixsofar, $joined)) {

                    $keyMap = array_combine($details->getId(), (array) $toMap->getPrimary());
					$first = true;
					foreach( $keyMap as $fromKey=>$toKey ) {
					    if ( !$first ) {
					        $localFrom .= " AND ";
					    } else {
					        $first = false;
					    }
					    $localFrom .= $lastAlias . '.'
							. $this->_adapter->quoteIdentifier($fromMap->untranslateField($fromKey))
					        . ' = ' . $alias . '.' . $this->_adapter->quoteIdentifier($toKey);
					}

					if ( $details->getFilters() ) {
						$translator = new Xyster_Db_Translator($this->_adapter);
						$translator->setRenameCallback(array($toMap, 'untranslateField'));
						$translator->setTable($alias);
						$fToken = $translator->translate($details->getFilters());
						$localFrom .= ' AND '. $fToken->getSql();
						$binds += $fToken->getBindValues();
					}
					$joined[] = $prefixsofar;
				}
				$lastAlias = $alias;
				$lastTable = $toMap->getTable();
				$container = $class;
				
				$from[ $joinTableSql ] = new Xyster_Db_Token($localFrom, $binds);
			}
		}

		return $from;
	}
}