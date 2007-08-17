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
 * @see Xyster_Db_Translator
 */
require_once 'Xyster/Db/Translator.php';
/**
 * @see Xyster_Orm_Query_Parser
 */
require_once 'Xyster/Orm/Query/Parser.php';
/**
 * A translator for db fields that is smart about ORM
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Mapper_Translator extends Xyster_Db_Translator
{
    /**
     * This is the anchor class for translations
     *
     * @var string 
     */	
	protected $_class;
	
	/**
	 * Associative array of prefixes to table aliases
	 *
	 * @var array
	 */
	protected $_aliases = array();

	/**
	 * Associative array of prefixes to table names
	 *
	 * @var array
	 */
	protected $_tables = array();
	
	/**
	 * The mapper factory
	 *
	 * @var Xyster_Orm_Mapper_Factory_Interface
	 */
	protected $_mapFactory;
	
	/**
	 * The query parser
	 *
	 * @var Xyster_Orm_Query_Parser
	 */
	protected $_parser;

	/**
	 * Creates a new orm sql translator
	 * {@inherit}
	 *
	 * @param Zend_Db_Adapter_Abstract $db
	 * @param string $className
	 */
	public function __construct( Zend_Db_Adapter_Abstract $db, $className, Xyster_Orm_Mapper_Factory_Interface $mapFactory )
	{
	    parent::__construct($db);
	    
	    require_once 'Xyster/Orm/Loader.php';
	    Xyster_Orm_Loader::loadEntityClass($className);
	    $this->_class = $className;
	    $this->_mapFactory = $mapFactory;
	    require_once 'Xyster/Orm/Query/Parser.php';
	    $this->_parser = new Xyster_Orm_Query_Parser($mapFactory);
	    $map = $mapFactory->get($className);
	    $this->aliasField(current($map->getEntityMeta()->getPrimary()));
	}
	
	/**
	 * Gets the main alias
	 * 
	 * @return string
	 */
	public function getMain()
	{
		return array_key_exists('', $this->_aliases) ? $this->_aliases[''] : null;
	}

	/**
	 * Returns an alias to prefix a column in a SQL query
	 *
	 * @param string $column  The column to alias
	 * @throws Xyster_Orm_Backend_Sql_Exception if $column is runtime
	 * @return string
	 */
	public function aliasField( $field )
	{
		if ( $this->_parser->isRuntime(Xyster_Data_Field::named($field), $this->_class) ) {
			require_once 'Xyster/Orm/Mapper/Exception.php';
			throw new Xyster_Orm_Mapper_Exception('Runtime fields cannot be aliased for the backend');
		}

		$factory = $this->_mapFactory;
		$className = $this->_class;
		$prefix = "";
        $currentMeta = $factory->getEntityMeta($className);
		$calls = Xyster_String::smartSplit('->',$field);
		
		if ( count($calls) > 1 ) {
			$prefixes = array();
			foreach( $calls as $call ) {
			    if ( $currentMeta->isRelation($call) ) {
    				$prefixes[] = $call;
				    $className = $currentMeta->getRelation($call)->getTo();
				    $currentMeta = $factory->getEntityMeta($className);
			    }
			}
			$prefix = implode('->', $prefixes) . '->';
		}

		if ( !in_array($prefix, array_keys($this->_tables)) ) {
			$tableName = $factory->get($className)->getTable();
			$num = count(array_keys($this->_tables, $tableName));
			$this->_tables[$prefix] = $tableName;
			$this->_aliases[$prefix] = preg_replace('/[`\]\s]*/i', '',
				$tableName) . $num;
		}

		return $this->_aliases[$prefix];
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
		$factory = $this->_mapFactory;
		$db = $this->_adapter;

		foreach( array_keys($this->_aliases) as $prefix ) {
			if ( $prefix == '' ) {
				continue;
			}

			$prefixes = explode('->',$prefix);
			$container = $this->_class;
			$prefixsofar = '';
			$lastTable = $this->_tables[''];
			$lastAlias = $this->getMain();

			foreach( $prefixes as $v ) {
				if ( !$v ) {
					continue;
				}

				$prefixsofar .= $v.'->';
				$fromMap = $factory->get($container);
				$fromMeta = $fromMap->getEntityMeta();
				$relation = $fromMeta->getRelation($v);
				$class = $relation->getTo();
				$toMap = $factory->get($class);
				$alias = $this->aliasField($prefixsofar.current($toMap->getEntityMeta()->getPrimary()));

				$binds = array();
			    $localFrom = array();
				
				if (!in_array($prefixsofar, $joined)) {

                    $keyMap = array_combine($relation->getId(), $toMap->getEntityMeta()->getPrimary());
					foreach( $keyMap as $fromKey=>$toKey ) {
					    $localFrom[] = $lastAlias . '.'
							. $db->quoteIdentifier($fromMap->untranslateField($fromKey))
					        . ' = ' . $alias . '.' . $toMap->untranslateField($db->quoteIdentifier($toKey));
					}

					if ( $relation->getFilters() ) {
						$translator = new Xyster_Db_Translator($db);
						$translator->setRenameCallback(array($toMap, 'untranslateField'));
						$translator->setTable($alias);
						$fToken = $translator->translate($relation->getFilters());
						$localFrom[] = $fToken->getSql();
						$binds += $fToken->getBindValues();
					}
					$joined[] = $prefixsofar;
				}
				$lastAlias = $alias;
				$lastTable = $toMap->getTable();
				$container = $class;
				
				$from[ $toMap->getTable() . ' AS ' . $alias ] =
				    new Xyster_Db_Token(implode(' AND ', $localFrom), $binds);
			}
		}

		return $from;
	}
	
	/**
	 * {@inherit}
	 *
	 * @param Xyster_Data_Field $field
	 * @return string
	 */
	protected function _getRenamedField( Xyster_Data_Field $field )
	{
	    $factory = $this->_mapFactory;
		$className = $this->_class;
		
	    $prefixes = Xyster_String::smartSplit('->', $field->getName());
		$meta = $factory->getEntityMeta($className);

		if ( count($prefixes) > 1 ) {
			foreach( $prefixes as $call ) {
				if ( $meta->isRelation($call) ) {
					$className = $meta->getRelation($call)->getTo();
		            $meta = $factory->getEntityMeta($className);
				}
			}
		}

		$rename = $factory->get($className)
			->untranslateField($prefixes[count($prefixes)-1]);
	    
	    return $rename;
	}
	
	/**
	 * {@inherit}
	 *
	 * @param Xyster_Data_Field $field
	 * @return string
	 */
	protected function _getTableName( Xyster_Data_Field $field )
	{
	    return $this->aliasField($field->getName());
	}
}