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
 * @package   Xyster_Dao
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Dao;
/**
 * The base exception interface for transient problems with Data access
 *
 * @category  Xyster
 * @package   Xyster_Dao
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Repository
{
    /**
     * Creates and persists a new entity using the values provided.
     * 
     * The implementing DAO should validate these values appropriately.
     * 
     * @param array $values The values to use to create the entity
     * @return object the entity created
     */
    function create(array $values);

    /**
     * Deletes the entity provided.
     *
     * @param object $entity the entity to delete
     */
    function delete($entity);

    /**
     * Searches for a single entity using the where criteria provided.
     *
     * The criteria array should have field names as keys and their expected
     * values. If the value is itself an array or other iterable, it acts an IN
     * criteria, instead of equals.
     *
     * @var array $criteria The criteria
     * @return object The entity found or null
     */
    function findOne(array $criteria);
    
    /**
     * Searches for all entities matching the where criteria provided.
     * 
     * The criteria array should have field names as keys and their expected
     * values. If the value is itself an array or other iterable, it acts an IN
     * criteria, instead of equals.
     * 
     * @var array $criteria The criteria
     * @return array The entities found or an empty array if none
     */
    function findAll(array $criteria);

    /**
     * Returns all entities of this type from the repository.
     *
     * @return array All entities in the repository
     */
    function getAll();

    /**
     * Gets an entity by ID
     *
     * @param mixed $id the identifier of the entity to find
     * @return object the entity found
     */
    function getById($id);
    
    /**
     * Gets several entities by their identifiers
     * 
     * @param array $ids The ids of the entities to load
     * @return array The entities found or an empty array if none
     */
    function getByIds(array $ids);
    
    /**
     * Gets an entity by ID and throws an exception if it wasn't found
     * 
     * @param mixed $id the identifier of the entity to find
     * @return object the entity found
     * @throws DataRetrievalException if the entity wasn't found
     */
    function requireById($id);

    /**
     * Gets the class of entity
     * 
     * @return ReflectionClass
     */
    function getEntityClass();

    /**
     * Takes the values in the entity and updates its persistent state.
     *
     * This entity should not be part of the ORM context, otherwise this behaves
     * as a normal update.
     *
     * @param object $entity The entity to merge
     */
    function merge($entity);

    /**
     * Persists the given entity to the data store.
     *
     * The implementing DAO should validate this entity appropriately.
     *
     * @param object $entity the entity to save
     */
    function persist($entity);

    /**
     * Refreshes the entity with new values from the ORM context.
     * 
     * @param object $entity the entity to refresh
     */
    function refresh($entity);

    /**
     * Updates the entity using the values provided and initiates an update
     *
     * For ORM systems which flush updates, and the entity isn't part of the
     * ORM context, this will just bind the values to the entity and no update
     * will occur until the flush.
     *
     * @param object $entity The entity to update
     * @param array $values The values to bind to the entity
     */
    function update($entity, array $values);
}
