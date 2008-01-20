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
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Responsible for translating data store records into entities
 * 
 * Fowler describes a data mapper as "A layer of Mappers that moves data between
 * objects and a database while keeping them independent of each other and the
 * mapper itself". {@link http://www.martinfowler.com/eaaCatalog/dataMapper.html}
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Orm_Mapper_Interface
{
    /**
     * Allows for subclassing without overwriting constructor
     *
     */
    function init();
    /**
     * Deletes an entity
     *
     * @param Xyster_Orm_Entity $entity The entity to delete
     */
    function delete( Xyster_Orm_Entity $entity );
    /**
     * Gets the first entity from the data store matching the criteria
     *
     * @param mixed $criteria
     * @return Xyster_Orm_Entity  The entity found
     */
    function find( $criteria );
    /**
     * Gets all entities from the data store matching the criteria
     *
     * @param mixed $criteria  
     * @param mixed $sorts
     * @return Xyster_Orm_Set  A collection of the entities
     */
    function findAll( $criteria, $sorts = null );
    /**
     * Gets an entity with the supplied identifier
     *
     * @param mixed $id  The id of the entity to get
     * @return Xyster_Orm_Entity  The data entity found, or null if none
     */
    function get( $id );
    /**
     * Gets all entities from the data store
     *
     * @param array $ids  An array of ids for which entities to retrieve
     * @return Xyster_Orm_Set  A collection of the entities
     */
    function getAll( array $ids = null );
    /**
     * Gets the name of the domain to which this mapper belongs
     * 
     * @return string  The domain
     */
    function getDomain();
    /**
     * Gets the entity metadata
     *
     * @return Xyster_Orm_Entity_Meta
     */
    function getEntityMeta();
    /**
     * Gets the class name of the entity
     *
     * @return string  The class name of the entity
     */
    function getEntityName();
    /**
     * Gets the factory that created the mapper
     *
     * @return Xyster_Orm_Mapper_Factory_Interface
     */
    function getFactory();
    /**
	 * Gets the fields for an entity as they appear in the backend
	 * 
	 * The array should come in the format of the describeTable method of the 
	 * Zend_Db_Adapter_Abstract class.
	 * 
	 * @see Zend_Db_Adapter_Abstract::describeTable()
	 * @return array
	 */
    function getFields();
    /**
     * Gets the columns that should be used to index the entity
     * 
     * The array should consist of index names as keys and arrays of the columns 
     * contained within as values.
     *
     * @return array
     */
    function getIndex();
    /**
     * Gets entities via a many-to-many table
     *
     * @param Xyster_Orm_Entity $entity
     * @param Xyster_Orm_Relation $relation
     * @return Xyster_Orm_Set
     */
    function getJoined( Xyster_Orm_Entity $entity, Xyster_Orm_Relation $relation );
    /**
	 * Gets the time in seconds an entity should be cached
	 *
	 * @return int
	 */
    function getLifetime();
    /**
     * Gets the value of an option
     *
     * @param string $name The name of the option
     * @return mixed The option value
     */
    function getOption( $name );
    /**
     * Gets the options for this mapper
     *
     * @return array
     */
    function getOptions();
    /**
     * Gets an empty entity set for the mapper's entity type
     *
     * @return Xyster_Orm_Set An empty set
     */
    function getSet( Xyster_Collection_Interface $entities = null );
    /**
     * Gets the table from which an entity comes
     * 
     * What the "table" means is up to the implementation.  In an RDBMS, this is
     * obvious.  If the mapper is for a set of XML documents, this might
     * represent the file name.  An LDAP implementation might be a DN with an
     * object type.
     * 
     * @return string The table name
     */
    function getTable();
    /**
	 * Performs a query
	 * 
	 * @param Xyster_Orm_Query $query  The query details
	 * @return Xyster_Data_Set
	 */
	function query( Xyster_Orm_Query $query ); 
    /**
	 * Reloads an entity's values with fresh ones from the backend
	 *
	 * @param Xyster_Orm_Entity $entity  The entity to refresh
	 */
    function refresh( Xyster_Orm_Entity $entity );
    /**
     * Saves an entity (insert or update)
     *
     * @param Xyster_Orm_Entity $entity  The entity to save
     */
    function save( Xyster_Orm_Entity $entity );
    /**
     * Translates the field from the original format into the entity field name
     * 
     * For instance, many database users prefer to use underscores for column 
     * names, and the class should have this as a camel case word.
     * (user_name vs. userName).
     * 
     * @param string $field
     * @return string
     */    
    function translateField( $field );
    /**
     * Translates the field from the entity format back into the original
     * 
     * For instance, many database users prefer to use underscores for column 
     * names, and the class should have this as a camel case word.
     * (user_name vs. userName).
     * 
     * @param string $field
     * @return string
     */    
    function untranslateField( $field );
}