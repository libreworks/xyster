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
 * @see Xyster_Orm_Backend_Abstract
 */
require_once 'Xyster/Orm/Backend/Abstract.php';
/**
 * An exception for Xyster_Orm
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Backend_Sql extends Xyster_Orm_Backend_Abstract
{
    /**
     * The data adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Information provided by the adapter's describeTable() method
     *
     * @var array
     */
    protected $_metadata = array();

    /**
     * Cache for information provided by the adapter's describeTable() method
     *
     * @var Zend_Cache_Core
     */
    protected $_metadataCache = null;
    
    /**
     * Default cache for information provided by the adapter's describeTable() method
     *
     * @var Zend_Cache_Core
     */
    protected static $_defaultMetadataCache = null;
    
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
        Zend_Registry::set( md5($dsn), Zend_Db::factory($driver,$config) );
    }

    /**
     * Sets the default metadata cache for information returned by getFields()
     *
     * If $defaultMetadataCache is null, then no metadata cache is used by
     * default.
     *
     * @param  mixed $metadataCache Either a Cache object, or a string naming a Registry key
     */
    public static function setDefaultMetadataCache($metadataCache = null)
    {
        self::$_defaultMetadataCache = self::_setupMetadataCache($metadataCache);
    }

    /**
     * Gets the default metadata cache for information returned by getFields()
     *
     * @return Zend_Cache_Core
     */
    public static function getDefaultMetadataCache()
    {
        return self::$_defaultMetadataCache;
    }
    
	/**
	 * Removes entities from the backend
	 *
	 * @param Xyster_Data_Criterion $where  The criteria on which to remove entities
	 */
	public function delete( Xyster_Data_Criterion $where )
	{
	    $translator = new Xyster_Db_Translator( $this->_getAdapter() );
		$translator->setRenameCallback( array($this->_mapper,'untranslateField') );
		$token = $translator->translateCriterion($where);
		$stmt = $this->_getAdapter()->prepare( "DELETE FROM "
			. $this->_getAdapter()->quoteIdentifier($this->_mapper->getTable())
		    . " WHERE " . $token->getSql() );
		$stmt->execute( $token->getBindValues() );
	}
    
	/**
	 * Returns one entity found by primary key
	 * 
	 * Criteria will either be a scalar value, in which case, an entity should 
	 * be found directly (ID=?), or it will be an array containing column names
	 * as keys and their expected values (VAL1=?,VAL2=?).
	 *
	 * @param mixed $id Primary key or keys
	 * @return Xyster_Orm_Entity The entity found
	 */
	public function findByPrimary( $id )
	{
	    $keyNames = (array) $this->_mapper->getPrimary();

	    if ( count($keyNames) > 1 ) {
	        
    	    if (!is_array($id) || count($id) != count($keyNames)) {
                require_once 'Xyster/Orm/Backend/Exception.php';
                throw new Xyster_Orm_Backend_Exception("Missing value(s) for the primary key");
            }
            $keyValues = array();
            foreach( $id as $name => $value ) {
                $dbName = $this->_mapper->untranslateField($name);
                if ( !in_array($dbName,$keyNames) ) {
                    require_once 'Xyster/Orm/Backend/Exception.php';
                    throw new Xyster_Orm_Backend_Exception("'$dbName' is not a primary key");
                }
                $keyValues[$dbName] = $value;
            }
            
	    } else {
	        
	        $keyValues = array( $keyNames[0] => $id );
	        
	    }
	    
	    $whereParts = array();
        foreach( $keyValues as $name => $value ) {
            $whereParts[ $this->_getAdapter()->quoteIdentifier($name) . ' = ?' ] = $value;
        }
        
        return $this->_mapEntity( $this->_fetch( $whereParts, null, 1 ) );
	}

	/**
	 * Returns one entity found by criteria 
	 * 
	 * If the value for a column is an array, the value should be any of the
	 * values in the array (VAL1 IN ?,?,? ).
	 * 
	 * @param mixed $criteria Array of Criteria, a {@link Xyster_Data_Criterion}
	 * @return Xyster_Orm_Entity The first entity found
	 */
	public function findByCriteria( $criteria )
	{
	    if ( $criteria instanceof Xyster_Data_Criterion ) {
	        
	        $translator = new Xyster_Db_Translator( $this->_getAdapter() );
		    $translator->setRenameCallback(array($this->mapper,'untranslateField'));
		    
		    $select = $this->_getAdapter()->select();
		    $select->from(array('t1', $this->_mapper->getTable()));

		    $binds = array();
			$token = $translator->translate($criteria);
			$select->where( $token->getSql() );
			$binds += $token->getBindValues();
			
			$find = $this->_getAdapter()->prepare($sql);
			$find->execute( $binds );

			return $this->_mapEntity( $find );
			
	    } else if ( is_array($criteria) ) {
	        
	        $this->_checkPropertyNames($criteria);
	        
	        $whereParts = array();
            foreach( $criteria as $name => $value ) {
                $expr = $this->_getAdapter()->quoteIdentifier($name) . ' = ?';
                if (is_array($value)) {
                    $orParts = array();
                    foreach( $value as $v ) {
                        $orParts[] = $this->_getAdapter()->quoteInto( $expr, $v );
                    }
                    $whereParts[] = '( ' . implode(' OR ',$orParts) . ' )';
                } else {
                    $whereParts[ $expr ] = $value;
                }
            }
            
            return $this->_mapEntity( $this->_fetch( $whereParts, null, 1 ) );
	    }
	}

    /**
     * Returns a collection of entities found by primary keys
     * 
     * @param array $ids The ids to retrieve
     * @return Xyster_Orm_Set The entities found
     */	
	public function findManyByPrimary( array $ids )
	{
	    $keyNames = (array) $this->_mapper->getPrimary();

	    $orWhere = array();
	    
	    if ( count($keyNames) > 1 ) {

	        foreach( $ids as $id ) {
	    	    if (!is_array($id) || count($id) != count($keyNames)) {
	                require_once 'Xyster/Orm/Backend/Exception.php';
	                throw new Xyster_Orm_Backend_Exception("Missing value(s) for the primary key");
	            }
	            
	            $andWhere = array();
	            foreach( $id as $name => $value ) {
	                $dbName = $this->_mapper->untranslateField($name);
	                if ( !in_array($dbName,$keyNames) ) {
	                    require_once 'Xyster/Orm/Backend/Exception.php';
	                    throw new Xyster_Orm_Backend_Exception("'$dbName' is not a primary key");
	                }
	                $andWhere[] = $this->_getAdapter()->quoteInto(
	                $this->_getAdapter()->quoteIdentifier($dbName) . " = ?", $value );
	            }
	            $orWhere[] = '( ' . implode(' AND ',$andWhere) . ' )';
	        }

	    } else {

	        foreach( $ids as $id ) {
	            $orWhere[] = $this->_getAdapter()->quoteInto(
	                $this->_getAdapter()->quoteIdentifier($keyNames[0]) . " = ?", $id); 
	        }
	        
	    }

	    $where = implode(' OR ', $orWhere);
	    
	    return $this->_mapSet( $this->_fetch( $where, null ) );
	}

	/**
	 * Returns a collection of entities found by criteria
	 * 
	 * @param mixed $criteria Array of Criteria, a {@link Xyster_Data_Criterion}, or null
	 * @param mixed $sort Array of Xyster_Data_Sort objects, a {@link Xyster_Data_Sort}, or null
	 * @return Xyster_Orm_Set The entities found
	 */
	public function findManyByCriteria( $criteria = null, $sort = null )
	{	    
	    $binds = array();

	    $select = $this->_getAdapter()->select();
		$select->from( array('t1' => $this->_mapper->getTable()),
		    $this->_selectColumns() );
	    
        $translator = new Xyster_Db_Translator( $this->_getAdapter() );
		$translator->setRenameCallback(array($this->_mapper,'untranslateField'));
		
	    if ( $criteria instanceof Xyster_Data_Criterion ) {

	        $token = $translator->translate($criteria);
			$select->where( $token->getSql() );
			$binds += $token->getBindValues();
			
	    } else if ( is_array($criteria) ) {
	        
	        $this->_checkPropertyNames($criteria);
	        
            foreach( $criteria as $name => $value ) {
                $expr = $this->_getAdapter()->quoteIdentifier($name);
            	if ( is_scalar($value) || $value === null ) {
					$select->where( $expr . ( ( $value === null ) ? " IS NULL" : " = :$name" ) );
					if ( $value !== null ) {
						$binds[':'.$name] = $value;
					}
				} else {
					$in = array();
					foreach( $value as $index=>$inval ) {
						$binds[':'.$name.$index] = $inval;
						$in[] = ':'.$name.$index;
					}
					$select->where( $expr . ' IN ( ' . implode(',',$in) . ' ) ' );
				}
            }
	    }
	    
	    if ( $sort !== null ) {
		    $sort = array($sort);
		    foreach( $sort as $s ) {
		        if (! $s instanceof Xyster_Data_Sort ) {
		            require_once 'Xyster/Orm/Backend/Exception.php';
                    throw new Xyster_Orm_Backend_Exception("The sort parameter must be a single Xyster_Data_Sort or an array with multiple");
		        } else {
		            $token = $translator->translateSort($s);
		            $select->order($token->getSql());
		        }
		    }
	    }
	    
		$find = $this->_getAdapter()->prepare($select);
		$find->execute( $binds );
	    
	    return $this->_mapSet( $find );
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
    public function getFields()
    {
        if ( !$this->_metadata ) {
            $this->_setupMetadata();
        }
        return $this->_metadata;
    }
    
    /**
     * Gets the metadata cache for information returned by getFields()
     *
     * @return Zend_Cache_Core or null
     */
    public function getMetadataCache()
    {
        return $this->_metadataCache;
    }

	/**
	 * Saves a new entity into the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to insert
	 * @return mixed  The new primary key
	 */
	public function insert( Xyster_Orm_Entity $entity )
	{
	    $data = array();
	    foreach( $entity->toArray() as $name => $value ) {
	        $data[ $this->_mapper->untranslateField($name) ] = $value; 
	    }
	    
        /**
         * This class assumes that if you have a compound primary key
         * and one of the columns in the key uses a sequence,
         * it's the _first_ column in the compound key.
         */
        $primary = (array) $this->_mapper->getPrimary();
        $pkIdentity = $primary[0];
        if ( count($primary) > 0 ) {  
	        $fields = $this->_mapper->getFields();
	        foreach( $fields as $field ) {
	            if ( $field->isIdentity() ) {
	                $posn = $field->getPrimaryPosition() - 1;
	                $pkIdentity = $primary[ $posn ];
	            }
	        }
        }

        $sequence = $this->_mapper->getSequence();
        if ( !$sequence && $this->_getAdapter() instanceof Zend_Db_Adapter_Pdo_Pgsql ) {
            $sequence = $this->_mapper->getTable() . "_" . $pkIdentity . "_seq";
        }
        
        /**
         * If this table uses a database sequence object and the data does not
         * specify a value, then get the next ID from the sequence and add it
         * to the row.  We assume that only the first column in a compound
         * primary key takes a value from a sequence.
         */
        if ( $this->_mapper->getSequence() && !$data[$pkIdentity]) {
            $data[$pkIdentity] = $this->_getAdapter()->nextSequenceId($this->_mapper->getSequence());
        }

        $this->_getAdapter()->insert($this->_mapper->getTable(), $data);

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

        if (!$this->_mapper->getSequence()) {
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
            $field = $this->_mapper->translateField($name);
            $entity->$field = $value;
        }
        
    	// this is in case any triggers in the db have changed the record
    	$this->refresh($entity);
    	
    	return $newPrimaryKey;
	}

	/**
	 * Reloads an entity's values with fresh ones from the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to refresh
	 */
	public function refresh( Xyster_Orm_Entity $entity )
	{
	    $where = $this->_getPrimaryKeyWhere($entity);
	    $this->_mapEntity( $this->_fetch( $where, null, 1 ), $entity );
	}

	/**
	 * Updates the values of an entity in the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to update
	 */
	public function update( Xyster_Orm_Entity $entity )
	{
	    $whereParts = $this->_getPrimaryKeyWhere($entity,true);
	    
	    $where = array();
	    foreach( $whereParts as $sql => $bind ) {
	        $where[] = $this->_getAdapter()->quoteInto($sql,$bind);
	    }
	    
    	$values = array();
    	foreach( $entity->getDirtyFields() as $name => $value ) {
    	    $values[ $this->_mapper->untranslateField($name) ] = $value;
    	}
    	
    	if ( count($values) > 0 ) {
    	    $this->_getAdapter()->update( $this->_mapper->getTable(), $values, $where );
    	}
    	
    	// this is in case any triggers in the db have changed the record
    	$this->refresh($entity);
	}

    /**
     * Support method for fetching rows.
     *
     * @param  mixed $where  OPTIONAL An SQL WHERE clause.
     * @param  mixed $order  OPTIONAL An SQL ORDER clause.
     * @param  int          $count  OPTIONAL An SQL LIMIT count.
     * @param  int          $offset OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Statement_Interface The row results, in FETCH_ASSOC mode.
     */
    protected function _fetch($where = null, $order = null, $count = null, $offset = null)
    {
        // selection tool
        $select = $this->_getAdapter()->select();

        // the FROM clause
        $select->from($this->_mapper->getTable(), $this->_selectColumns());

        // the WHERE clause
        $where = (array) $where;
        foreach ($where as $key => $val) {
            // is $key an int?
            if (is_int($key)) {
                // $val is the full condition
                $select->where($val);
            } else {
                // $key is the condition with placeholder,
                // and $val is quoted into the condition
                $select->where($key, $val);
            }
        }

        // the ORDER clause
        $order = (array) $order;
        foreach ($order as $val) {
            $select->order($val);
        }

        // the LIMIT clause
        $select->limit($count, $offset);

        // return the results
        return $this->_getAdapter()->query($select);
    }
    
    /**
	 * Gets a connection to the database
	 *
	 * @return Zend_Db_Adapter_Abstract A connection to the database
	 * @throws Xyster_Orm_Backend_Exception
	 */
	protected function _getAdapter()
	{
	    if ( $this->_db instanceof Zend_Db_Adapter_Abstract ) {
	        return $this->_db;
	    }

		$dsn = $this->_mapper->getDomain();
		
        if ($dsn === null) {
            return null;
        }
        require_once 'Zend/Registry.php';
        $db = Zend_Registry::get( md5($dsn) );
        if (!$db instanceof Zend_Db_Adapter_Abstract) {
            require_once 'Xyster/Orm/Backend/Exception.php';
            throw new Xyster_Orm_Backend_Exception('Argument must be a Registry key where a Zend_Db_Adapter_Abstract object is stored');
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
	    $keyNames = (array) $this->_mapper->getPrimary();
	    $key = $entity->getPrimaryKey($base);
	    
	    $whereParts = array();
	    foreach( $keyNames as $name ) {
    	    $whereParts[ $this->_getAdapter()->quoteIdentifier($name) . ' = ?' ] = 
    	        $key[ $this->_mapper->translateField($name) ];
    	}
    	
    	return $whereParts;
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
		$entities = Xyster_Collection::using($entities);
		return $this->_mapper->getSet($entities);
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
			$alias = $this->_mapper->translateField($name);
			$columns[$alias] = $name;
		}
		return $columns;
	}
    
    /**
     * Sets the metadata cache for information returned by getFields().
     *
     * If $metadataCache is null, then no metadata cache is used.
     * 
     * @param  mixed $metadataCache Either a Cache object, or a string naming a Registry key
     * @return Zend_Db_Table_Abstract Provides a fluent interface
     */
    protected function _setMetadataCache($metadataCache)
    {
        $this->_metadataCache = self::_setupMetadataCache($metadataCache);
        return $this;
    }

    /**
     * Initializes metadata.
     *
     * If metadata cannot be loaded from cache, adapter's describeTable() method
     * is called to discover metadata information. Returns true if and only if
     * the metadata are loaded from cache.
     *
     * @return boolean
     * @throws Xyster_Orm_Backend_Exception
     */
    protected function _setupMetadata()
    {
        // Assume that metadata will be loaded from cache
        $isMetadataFromCache = true;

        // If $this has no metadata cache but the class has a default metadata cache
        if (null === $this->_metadataCache && null !== self::$_defaultMetadataCache) {
            // Make $this use the default metadata cache of the class
            $this->_setMetadataCache(self::$_defaultMetadataCache);
        }

        // If $this has a metadata cache
        if (null !== $this->_metadataCache) {
            // Define the cache identifier where the metadata are saved
            $cacheId = md5($this->_mapper->getTable());
        }

        // If $this has no metadata cache or metadata cache misses
        if (null === $this->_metadataCache || !($metadata = $this->_metadataCache->load($cacheId))) {
            // Metadata are not loaded from cache
            $isMetadataFromCache = false;
            // Fetch metadata from the adapter's describeTable() method
            $metadata = $this->_getAdapter()->describeTable($this->_mapper->getTable());
            // If $this has a metadata cache, then cache the metadata
            if (null !== $this->_metadataCache && !$this->_metadataCache->save($metadata, $cacheId)) {
                /**
                 * @see Xyster_Orm_Backend_Exception
                 */
                require_once 'Xyster/Orm/Backend/Exception.php';
                throw new Xyster_Orm_Backend_Exception('Failed saving metadata to metadataCache');
            }
        }

        // Assign the metadata to $this
        $this->_metadata = $metadata;

        // Return whether the metadata were loaded from cache
        return $isMetadataFromCache;
    }
	
    /**
     * @param mixed $metadataCache Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Zend_Db_Table_Exception
     */
    protected static final function _setupMetadataCache($metadataCache)
    {
        if ($metadataCache === null) {
            return null;
        }
        if (is_string($metadataCache)) {
            require_once 'Zend/Registry.php';
            $metadataCache = Zend_Registry::get($metadataCache);
        }
        if (!$metadataCache instanceof Zend_Cache_Core) {
            require_once 'Xyster/Orm/Backend/Exception.php';
            throw new Xyster_Orm_Backend_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $metadataCache;
    }
}