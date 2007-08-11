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
 * @see Xyster_Orm_Mapper_Abstract
 */
require_once 'Xyster/Orm/Mapper/Abstract.php';
/**
 * Responsible for translating data store records into entities
 * 
 * Fowler describes a data mapper as "A layer of Mappers that moves data between
 * objects and a database while keeping them independent of each other and the
 * mapper itself". {@link http://www.martinfowler.com/eaaCatalog/dataMapper.html}
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Mapper extends Xyster_Orm_Mapper_Abstract
{
    /**
     * The data adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    
    /**
     * Information provided by the getFields() method
     *
     * @var array
     * @see getFields()
     */
    protected $_metadata = array();

    /**
     * Cache for information provided the backend's getFields method
     *
     * @var Zend_Cache_Core
     */
    protected $_metadataCache;
    
    /**
     * Creates a new mapper
     * 
     */
    final public function __construct( Zend_Cache_Core $cache )
    {
        if ( $cache ) {
            $this->_metadataCache = $cache;
        } else if ( array_key_exists('metadataCache', $this->_options) ) {
            $this->_metadataCache = self::_setupMetadataCache($this->_options['metadataCache']); 
        }
    }
    
    /**
     * Allows for customization of mapper at construction
     * 
     * Class authors should override this method.
     */
    public function init()
    {
    }
    
    /**
     * Sets up a nickname for a database adapter
     * 
     * This method adds a Zend_Db_Adapter_Abstract to the Zend_Registry so it 
     * can be retrieved later.
     * 
     * @param string $dsn
     * @param string $driver
     * @param array $config
     */
    static public function dsn( $dsn, $driver, array $config = array() )
    {
        require_once 'Zend/Registry.php';
        require_once 'Zend/Db.php';
        Zend_Registry::set(md5($dsn), Zend_Db::factory($driver, $config));
    }

    /**
     * Gets the first entity from the data store matching the criteria
     *
     * @param mixed $criteria
     * @return Xyster_Orm_Entity  The entity found
     */
    final public function find( $criteria ) 
    {
        $_criteria = $this->_buildCriteria($criteria);
		return $this->_mapEntity($this->_fetchOne($_criteria));
    }

    /**
     * Gets all entities from the data store matching the criteria
     *
     * @param mixed $criteria  
     * @param mixed $sorts
     * @return Xyster_Orm_Set  A collection of the entities
     */
    final public function findAll( $criteria, $sorts = null )
    {
	    $select = $this->_buildSimpleSelect();
        $translator = $this->_buildTranslator();
        
        $token = $translator->translate($this->_buildCriteria($criteria));
		$select->where($token->getSql());
		$binds = $token->getBindValues();
	    
	    if ( $sort !== null ) {
		    $sort = array($sort);
		    foreach( $sort as $s ) {
		        if (! $s instanceof Xyster_Data_Sort ) {
		            require_once 'Xyster/Orm/Mapper/Exception.php';
                    throw new Xyster_Orm_Mapper_Exception("The sort parameter must be a single Xyster_Data_Sort or an array with multiple");
		        } else {
		            $token = $translator->translateSort($s);
		            $select->order($token->getSql());
		        }
		    }
	    }
	    
	    return $this->_mapSet($this->_getAdapter()->query($select, $binds));
    }

    /**
     * Gets all entities from the data store
     *
     * @param array $ids  An array of ids for which entities to retrieve
     * @return Xyster_Orm_Set  A collection of the entities
     */
    final public function getAll( array $ids = null )
    {
	    $orWhere = array();
	    if ( !$ids ) {
	        $ids = array();
	    }

        foreach( $ids as $id ) {
    	    $id = $this->_checkPrimaryKey($id);
            $orWhere[] = $this->_buildCriteria($id);
        }

	    $where = ( count($orWhere) ) ?
	        Xyster_Data_Junction::fromArray('OR', $orWhere) : null;
	    
	    $select = $this->_buildSimpleSelect();
        $translator = $this->_buildTranslator();
        $binds = array();
        
        if ( $where ) {
            $token = $translator->translate($where);
    		$select->where($token->getSql());
		    $binds += $token->getBindValues();
        }
	        
	    return $this->_mapSet($this->_getAdapter()->query($select, $binds));
    }
        
    /**
	 * Gets the fields for an entity as they appear in the backend
	 * 
	 * The array should come in the format of the describeTable method of the 
	 * Zend_Db_Adapter_Abstract class.
	 * 
	 * @see Zend_Db_Adapter_Abstract::describeTable
	 * @return array
	 */
    final public function getFields()
    {
        if ( !$this->_metadata ) {

            $cache = $this->getMetadataCache();
            
            // If $this has a metadata cache
	        if (null !== $cache) {
	            // Define the cache identifier where the metadata are saved
	            $cacheId = md5($this->getTable());
	        }
	
	        // If $this has no metadata cache or metadata cache misses
	        if (null === $cache || !($metadata = $cache->load($cacheId))) {
	            // Fetch metadata from the adapter's describeTable() method
	            $metadata = $this->_getAdapter()->describeTable($this->getTable());
	            // If $this has a metadata cache, then cache the metadata
	            if (null !== $cache && !$cache->save($metadata, $cacheId)) {
	                require_once 'Xyster/Orm/Mapper/Exception.php';
	                throw new Xyster_Orm_Mapper_Exception('Failed saving metadata to metadataCache');
	            }
	        }
	
	        // Assign the metadata to $this
	        $this->_metadata = $metadata;
        }
        return $this->_metadata;
    }
    
    /**
     * Gets entities via a many-to-many table
     *
     * @param Xyster_Orm_Entity $entity
     * @param Xyster_Orm_Relation $relation
     * @return Xyster_Orm_Set
     */
    public function getJoined( Xyster_Orm_Entity $entity, Xyster_Orm_Relation $relation )
    {
        $leftMap = $this->_factory->get($relation->getFrom());
        $rightMap = $this->_factory->get($relation->getTo());
        
        $targetTable = $rightMap->getTable();
		$targetTableAlias = 't2';
        $columns = array();
		foreach( $rightMap->getFields() as $name => $v ) {
			$alias = $rightMap->translateField($name);
			$columns[$alias] = $targetTableAlias.'.'.$name;
		}

		// get the join SQL for the left to the middle
		$firstCond = '';
		$left = $relation->getLeft();
		foreach( $leftMap->getEntityMeta()->getPrimary() as $k=>$primary ) {
		    if ( $k > 0 ) {
		        $firstCond .= ' AND ';
		    }
		    $firstCond = 't1.' . $map->untranslateField($primary) . ' = ' .
		        $relation->getTable() . '.' . $left[$k];
		}
		
		// get the join SQL for the middle to the right 
		$secondCond = '';
		$right = $relation->getRight();
		foreach( $rightMap->getEntityMeta()->getPrimary() as $k=>$primary ) {
		    if ( $k > 0 ) {
		        $secondCond .= ' AND ';
		    }
		    $secondCond = $relation->getTable() . '.' . $right[$k] . ' = ' .
		        $targetTableAlias . '.' . $rightMap->untranslateField($primary); 
		}
		
		$select = $this->_getAdapter()->select();
		
		$binds = array();
		$select->from(array('t1', $this->getTable()), array())
		    ->join($relation->getTable(), $firstCond, array())
		    ->join(array($targetTableAlias,$targetTable), $secondCond, $columns);
		    		
		return $this->_mapSet($this->_getAdapter()->query($select, $binds));		
    }
    
    /**
     * Gets the metadata cache
     *
     * @return Zend_Cache_Core
     */
    final public function getMetadataCache()
    {
        return $this->_metadataCache;
    }

    /**
     * Gets the sequence of this table
     * 
     * @return string The sequence
     */
    final public function getSequence()
    {
        return $this->getOption('sequence');
    }

    /**
	 * Performs a query
	 * 
	 * @param Xyster_Orm_Query $query  The query details
	 * @return Xyster_Data_Set
	 */
	public function query( Xyster_Orm_Query $query ) 
	{
	    // we might as well require this now...
        require_once 'Xyster/Data/Set.php';
        require_once 'Xyster/Orm/Mapper/Translator.php';
		$translator = new Xyster_Orm_Mapper_Translator($this->_getAdapter(),
		    $this->getEntityName(), $this->_factory);

		$select = $this->_getAdapter()->select();
		$binds = array();
		
		// apply the where clause that can be run on the database
		foreach( $query->getBackendWhere() as $criterion ) {
			$whereToken = $translator->translateCriterion($criterion);
			$select->where( $whereToken->getSql() );
			$binds += $whereToken->getBindValues();
		}

		// apply the order by clause that can be run on the database
		if ( !$query->hasRuntimeOrder() ) {
			foreach( $query->getOrder() as $sort ) {
				$select->order($translator->translateSort($sort, false)->getSql());
			}
		}
		
		// if the query is entirely against the database, add limit & offset
		if ( !$query->isRuntime() && $query->getLimit() ) {
		    $select->limit($query->getLimit(), $query->getOffset());
		}
		
		if (! $query instanceof Xyster_Orm_Query_Report ) {

		    // add the from clause, joins, and columns
			$select->from(array($translator->getMain() => $this->getTable()), $this->_selectColumns());
			foreach( $translator->getFromClause() as $table => $joinToken ) {
			    $select->joinLeft($table, $joinToken->getSql(), array());
			    $binds += $joinToken->getBindValues();
			}
			
			return $this->_mapSet($this->_getAdapter()->query($select, $binds));

		} else {
            
		    // pretty self explanitory...
	        if ( $query->isDistinct() ) {
	            $select->distinct();
	        }

	        $fields = array();
			if ( !$query->isRuntime() ) {

				foreach( $query->getFields() as $k=>$field ) {
				    // we want to quote the field names for aggregates!
	                // Zend_Db_Select does not do this for functions
				    $quote = (! $field instanceof Xyster_Data_Field_Aggregate);
				    $fieldName = $translator->translateField($field, $quote)->getSql();
				    if ( $field->getAlias() == $field->getName() ) {
				        $fields[] = $fieldName;
				    } else {
				        $fields[$field->getAlias()] = $fieldName;
				    }
				}
				
				if ( count($query->getGroup()) ) {
				    // add the group clause 
					foreach( $query->getGroup() as $k=>$grp ) {
						$fieldName = $translator->translateField($grp, false)->getSql();
						if ( $grp->getAlias() == $grp->getName() ) {
						    $fields[] = $fieldName;
						} else {
						    $fields[$grp->getAlias()] = $fieldName;
						}
						$select->group($fieldName);
					}
					// add the having clause
					foreach( $query->getHaving() as $k=>$crit ) {
						$whereToken = $translator->translateCriterion($crit);
						$select->having($whereToken->getSql());
						$binds += $whereToken->getBindValues();
					}
				}

			} else {
			    // it's runtime, just pull back all fields in the main table
			    $fields = $this->_selectColumns();
			}
			
			// add the from clause, joins, and columns
		    $select->from(array($translator->getMain() => $this->getTable()), $fields);
			foreach( $translator->getFromClause() as $table => $joinToken ) {
			    $select->joinLeft($table, $joinToken->getSql(), array());
			    $binds += $joinToken->getBindValues();
			}

			$db = $this->_getAdapter();

			if ( !$query->isRuntime() ) {
			    $result = $db->query($select, $binds)->fetchAll(Zend_Db::FETCH_ASSOC);
				return new Xyster_Data_Set(Xyster_Collection::using($result));
			} else {
			    return $this->_mapSet($db->query($select, $binds)); 
			}
		}
	}
    
    /**
	 * Reloads an entity's values with fresh ones from the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to refresh
	 */
	public function refresh( Xyster_Orm_Entity $entity )
	{
	    $this->_mapEntity($this->_fetchOne($entity->getPrimaryKeyAsCriterion()), $entity);
	}
    
    /**
     * Gets a simple Select object for this table 
     *
     * @return Zend_Db_Select
     */
    protected function _buildSimpleSelect()
    {
        $select = $this->_getAdapter()->select();
		$select->from(array('t1' => $this->getTable()), $this->_selectColumns());

		return $select;
    }
    
    /**
     * Gets a Mapper Translator object
     *
     * @return Xyster_Db_Translator
     */
    protected function _buildTranslator()
    {
        require_once 'Xyster/Db/Translator.php';
		$translator = new Xyster_Db_Translator($this->_getAdapter());
		$translator->setRenameCallback(array($this, 'untranslateField'));
		return $translator;
    }
	
	/**
	 * Removes entities from the backend
	 *
	 * @param Xyster_Data_Criterion $where The criteria on which to remove entities
	 */
	protected function _delete( Xyster_Data_Criterion $where )
	{
	    $translator = $this->_buildTranslator();
		$token = $translator->translateCriterion($where);
		
		$stmt = $this->_getAdapter()->prepare('DELETE FROM '
			. $this->_getAdapter()->quoteIdentifier($this->getTable())
		    . ' WHERE ' . $token->getSql());
		$stmt->execute($token->getBindValues());
	}
	
	/**
	 * Fetches one record
	 *
	 * @param Xyster_Data_Criterion $where
	 * @return Zend_Db_Statement_Interface
	 */
	protected function _fetchOne( Xyster_Data_Criterion $where )
	{
	    $select = $this->_buildSimpleSelect();
		$select->limit(1);

		$translator = $this->_buildTranslator();
	    $token = $translator->translate($where);
		$select->where($token->getSql());

		return $this->_getAdapter()->query($select, $token->getBindValues());
	}
	
	/**
	 * Gets a connection to the database
	 *
	 * @return Zend_Db_Adapter_Abstract A connection to the database
	 * @throws Xyster_Orm_Mapper_Exception
	 */
	final protected function _getAdapter()
	{
	    if ( $this->_db instanceof Zend_Db_Adapter_Abstract ) {
	        return $this->_db;
	    }

		$dsn = $this->getDomain();
		
        if ($dsn === null) {
            return null;
        }
        require_once 'Zend/Registry.php';
        $db = Zend_Registry::get( md5($dsn) );
        if (!$db instanceof Zend_Db_Adapter_Abstract) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('Argument must be a Registry key where a Zend_Db_Adapter_Abstract object is stored');
        }
        return $db;
    }
    
    /**
	 * Gets the where items for the primary key of the entity
	 * 
	 * @param Xyster_Orm_Entity $entity The entity whose key is used
	 * @param boolean $base True to get the original primary key (if changed)
	 * @return array 
	 */
	protected function _getPrimaryKeyWhere( Xyster_Orm_Entity $entity, $base = false )
	{
	    $keyNames = $this->getEntityMeta()->getPrimary();
	    $key = $entity->getPrimaryKey($base);
	    
	    $whereParts = array();
	    foreach( $keyNames as $name ) {
    	    $whereParts[ $this->_getAdapter()->quoteIdentifier($this->untranslateField($name)) . ' = ?' ] = 
    	        $key[$name];
    	}
    	
    	return $whereParts;
	}	

    /**
	 * Saves a new entity into the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to insert
	 * @return mixed  The new primary key
	 */
	protected function _insert( Xyster_Orm_Entity $entity )
	{
	    $data = array();
	    foreach( $entity->toArray() as $name => $value ) {
	        $data[ $this->untranslateField($name) ] = $value; 
	    }
	    
        /**
         * This class assumes that if you have a compound primary key
         * and one of the columns in the key uses a sequence,
         * it's the _first_ column in the compound key.
         */
        $primary = array_map(array($this, 'untranslateField'), $this->getEntityMeta()->getPrimary());
        $pkIdentity = $primary[0];
        if ( count($primary) > 0 ) {  
	        $fields = $this->getEntityMeta()->getFields();
	        foreach( $fields as $field ) {
	            /* @var $field Xyster_Orm_Entity_Field */
	            if ( $field->isIdentity() ) {
	                $posn = $field->getPrimaryPosition() - 1;
	                $pkIdentity = $primary[ $posn ];
	            }
	        }
        }

        $sequence = $this->getSequence();
        if ( !$sequence && $this->_getAdapter() instanceof Zend_Db_Adapter_Pdo_Pgsql ) {
            $sequence = $this->getTable() . "_" . $pkIdentity . "_seq";
        }
        
        /**
         * If this table uses a database sequence object and the data does not
         * specify a value, then get the next ID from the sequence and add it
         * to the row.  We assume that only the first column in a compound
         * primary key takes a value from a sequence.
         */
        if ( $this->getSequence() && !$data[$pkIdentity]) {
            $data[$pkIdentity] = $this->_getAdapter()->nextSequenceId($this->getSequence());
        }

        $this->_getAdapter()->insert($this->getTable(), $data);

        if ( $data[$pkIdentity] ) {
            /**
             * Return the primary key value or array of values(s) if the
             * primary key is compound.  This handles the case of natural keys
             * and sequence-driven keys.  This also covers the case of
             * auto-increment keys when the user specifies a value
             */
            $pkData = array_intersect_key($data, array_flip($primary));
            if (count($primary) == 1) {
                $primaryKey = current($pkData);
            } else {
                $primaryKey = $pkData;
            }
        }

        if (!$this->getSequence()) {
            /**
             * Return the most recent ID generated by an auto-increment column
             */
            $primaryKey = $this->_getAdapter()->lastInsertId();
        }
        
        /**
         * Normalize the result to an array indexed by primary key column(s).
         */
        if (is_array($primaryKey)) {
            $newPrimaryKey = $primaryKey;
        } else {
            $newPrimaryKey = array(current($primary) => $primaryKey);
        }

        foreach( $newPrimaryKey as $name => $value ) {
            $field = $this->translateField($name);
            $entity->$field = $value;
        }
        
    	// this is in case any triggers in the db have changed the record
    	$this->refresh($entity);
    	
    	return $newPrimaryKey;
	}
    
	/**
	 * Translates the first row of a database recordset into an entity
	 *
	 * @param Zend_Db_Statement_Interface $stmt A statement containing the row to translate
	 * @param Xyster_Orm_Entity $entity  Optional.  An entity to refresh
	 * @return Xyster_Orm_Entity  The translated entity
	 */
	protected function _mapEntity( Zend_Db_Statement_Interface $stmt, Xyster_Orm_Entity $entity = null )
	{
	    $return = null;

		if ( $row = $stmt->fetch(Zend_Db::FETCH_ASSOC) ) {
			$this->_checkPropertyNames($row);
			$stmt->closeCursor();
			$return = ( $entity ) ? $entity->import($row) : $this->_create($row);
		}
		
		return $return;
	}
    
    /**
	 * Translates a database recordset into an entity set
	 *
	 * @param Zend_Db_Statement_Interface $stmt A statement containing rows to translate
	 * @return Xyster_Orm_Set  The translated set
	 */
	protected function _mapSet( Zend_Db_Statement_Interface $stmt )
	{
		$entities = array();
		$stmt->setFetchMode( Zend_Db::FETCH_ASSOC );
		foreach( $stmt->fetchAll() as $k => $row ) {
			if ( $k<1 ) {
				$this->_checkPropertyNames($row);
			}
			$entities[] = $this->_create($row);
		}
		$stmt->closeCursor();

		return $this->getSet( Xyster_Collection::using($entities) );
	}

	/**
	 * Gets the columns to select
	 *
	 * @return array
	 */
	protected function _selectColumns()
	{
	    $columns = array();
		foreach( $this->getFields() as $name => $v ) {
			$alias = $this->translateField($name);
			$columns[$alias] = $name;
		}
		return $columns;
	}
		
	/**
	 * Updates the values of an entity in the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to update
	 */
	protected function _update( Xyster_Orm_Entity $entity )
	{
	    $whereParts = $this->_getPrimaryKeyWhere($entity, true);
	    
	    $where = array();
	    foreach( $whereParts as $sql => $bind ) {
	        $where[] = $this->_getAdapter()->quoteInto($sql, $bind);
	    }
	    
    	$values = array();
    	foreach( $entity->getDirtyFields() as $name => $value ) {
    	    $values[ $this->untranslateField($name) ] = $value;
    	}
    	
    	if ( count($values) > 0 ) {
    	    $this->_getAdapter()->update($this->getTable(), $values, $where);
    	}
    	
    	// this is in case any triggers in the db have changed the record
    	$this->refresh($entity);
	}
    
    /**
     * @param mixed $metadataCache Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Xyster_Orm_Mapper_Exception
     */
    protected final function _setupMetadataCache($metadataCache)
    {
        if ($metadataCache === null) {
            return null;
        }
        if (is_string($metadataCache)) {
            require_once 'Zend/Registry.php';
            $metadataCache = Zend_Registry::get($metadataCache);
        }
        if (!$metadataCache instanceof Zend_Cache_Core) {
            require_once 'Xyster/Orm/Mapper/Exception.php';
            throw new Xyster_Orm_Mapper_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $metadataCache;
    }
}