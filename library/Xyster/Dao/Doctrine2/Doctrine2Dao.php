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
namespace Xyster\Dao\Doctrine2;
use Doctrine\ORM\EntityManager,
        Doctrine\ORM\EntityRepository;
/**
 * A DAO for Doctrine 2.0.
 *
 * You can use this class as-is, or extend it easily for your own business
 * logic.
 *
 * @category  Xyster
 * @package   Xyster_Dao
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Doctrine2Dao extends \Xyster\Dao\AbstractDao implements \Xyster\Dao\Repository
{
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var \ReflectionClass
     */
    protected $entityClass;
    /**
     * @var string
     */
    protected $entityName;
    /**
     * @var EntityRepository
     */
    protected $entityRepository;

    /**
     * Creates a new Doctrine DAO.
     *
     * @param mixed $entityClass A ReflectionClass, Xyster\Type\Type, or string
     */
    public function __construct($entityClass = null)
    {
        if ($entityClass != null){
            $this->setEntityClass($entityClass);
        }
    }

    /**
     * Creates and persists a new entity using the values provided.
     *
     * The implementing DAO should validate these values appropriately.
     *
     * @param array $values The values to use to create the entity
     * @return object the entity created
     */
    public function create(array $values)
    {
        $entity = $this->entityClass->newInstance();
        $binder = new Xyster\Type\Binder($entity);
        $binder->bind($values);
        $this->doExecute(function(EntityManager $em) use ($entity)
        {
            $em->persist($entity);
        });
    }

    /**
     * Deletes the entity provided.
     *
     * @param object $entity the entity to delete
     */
    public function delete($entity)
    {
        $this->doExecute(function(EntityManager $em) use ($entity)
        {
            $em->remove($entity);
        });
    }

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
    public function findOne(array $criteria)
    {
        return $this->doExecuteInRepository(function(EntityRepository $repo) use ($criteria)
        {
            /* @todo Convert this into a Doctrine query. */
            return $repo->findOneBy($criteria);
        });
    }

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
    public function findAll(array $criteria)
    {
        return $this->doExecuteInRepository(function(EntityRepository $repo) use ($criteria)
        {
            /* @todo Convert this into a Doctrine query. */
            return $repo->findBy($criteria);
        });
    }

    /**
     * Returns all entities of this type from the repository.
     *
     * @param array All entities in the repository
     */
    public function getAll()
    {
        return $this->doExecuteInRepository(function(EntityRepository $repo)
        {
            return $repo->findAll();
        });
    }

    /**
     * Gets an entity by ID
     *
     * @param mixed $id the identifier of the entity to find
     */
    public function getById($id)
    {
        $entityName = $this->entityName;
        return $this->doExecuteInRepository(function(EntityRepository $repo) use ($id)
        {
            return $repo->find($id);
        });
    }

    /**
     * Gets several entities by their identifiers.
     *
     * This only works with entities that have a scalar primary key
     *
     * @param array $ids The ids of the entities to load
     * @return array The entities found
     */
    public function getByIds(array $ids)
    {
        if ( !$ids ) {
            return array();
        }
        $entityName = $this->entityName;
        return $this->doExecute(function(EntityManager $em) use ($entityName, $ids)
        {
            $field = $em->getClassMetadata($entityName)->getSingleIdentifierFieldName();
            $qb = $em->createQueryBuilder();
            return $qb->select('t')
                ->from($entityName, 't')
                ->where($qb->expr()->in($field, QueryHelper::argumentsToParameters($ids)))
                ->setParameters(\array_combine(\range(1, count($ids)), $ids))
                ->getQuery()
                ->getResult();
        });
    }

    /**
     * Gets the entity type
     *
     * @return \ReflectionClass gets the entity type
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Gets the EntityManager.
     *
     * @return EntityManager gets the Doctrine 2 entityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Takes the values in the entity and updates its persistent state.
     *
     * This entity should not be part of the ORM context, otherwise this behaves
     * as a normal update.
     *
     * @param object $entity The entity to merge
     */
    public function merge($entity)
    {
        $this->doExecute(function(EntityManager $em) use ($entity)
        {
            $em->merge($entity);
        });
    }

    /**
     * Gets an entity by ID and throws an exception if it wasn't found
     *
     * @param mixed $id the identifier of the entity to find
     * @return object The entity found
     * @throws \Xyster\Dao\DataRetrievalException if the entity wasn't found
     */
    public function requireById($id)
    {
        $entity = $this->doExecuteInRepository(function(EntityRepository $repo) use ($id)
        {
            return $repo->find($id);
        });
        if ( !$entity ) {
            throw new \Xyster\Dao\DataRetrievalException("Could not load entity " . $this->entityName . " with id " . $id);
        }
        return $entity;
    }

    /**
     * Persists the given entity to the data store.
     *
     * The implementing DAO should validate this entity appropriately.
     *
     * @param object $entity the entity to save
     */
    public function persist($entity)
    {
        $this->doExecute(function(EntityManager $em) use ($entity)
        {
            $em->persist($entity);
        });
    }

    /**
     * Refreshes the entity with new values from the ORM context.
     *
     * @param object $entity the entity to refresh
     */
    public function refresh($entity)
    {
        $this->doExecute(function(EntityManager $em) use ($entity)
        {
            $em->refresh($entity);
        });
    }

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
    public function update($entity, array $values)
    {
        $binder = new Xyster\Type\Binder($entity);
        $binder->bind($values);
        $this->doExecute(function(EntityManager $em) use ($entity)
        {
            $em->merge($entity);
        });
    }

    /**
     * Sets the entity class this DAO uses.
     *
     * @param mixed $entityClass A ReflectionClass, Xyster\Type\Type, or string
     * @return Doctrine2Dao provides a fluent interface
     */
    public function setEntityClass($entityClass)
    {
        if ($entityClass instanceof \ReflectionClass) {
            $this->entityClass = $entityClass;
        } else if ($entityClass instanceof \Xyster\Type\Type){
            $this->entityClass = $entityClass->getClass();
        } else if ($entityClass) {
            if ( !\class_exists('\\' . \ltrim($entityClass, '\\')) ) {
                throw new DoctrineSystemException("Class not found: " . $entityClass);
            }
            $this->entityClass = new \ReflectionClass('\\' . \ltrim($entityClass, '\\'));
        }
        return $this;
    }

    /**
     * Sets the entityManager to be used by this DAO
     *
     * @param EntityManager $entityManager the entityManager
     * @return Doctrine2Dao provides a fluent interface
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    protected function checkConfig()
    {
        if ( !$this->entityManager ) {
            throw new DoctrineSystemException("entityManager is required");
        }
        if ( !$this->entityClass ) {
            throw new DoctrineSystemException("entityClass is required");
        }
        $this->entityName = $this->entityClass->getName();
        $this->entityRepository = $this->entityManager->getRepository($this->entityName);
    }

    /**
     * Executes something in the context of the entityManager.
     *
     * Exceptions are caught and translated.
     *
     * @param Closure $cb The closure to execute, takes the entityManager
     * @return mixed whatever the function returns, this method also returns
     */
    protected function doExecute(Closure $cb)
    {
        try {
            return $cb($this->entityManager);
        } catch ( \Exception $e ) {
            throw self::translateException($e);
        }
    }

    /**
     * Executes something in the context of the entityRepository.
     *
     * Exceptions are caught and translated.
     *
     * @param Closure $cb The closure to execute, takes the entityRepository
     * @return mixed whatever the function returns, this method also returns
     */
    protected function doExecuteInRepository(Closure $cb)
    {
        try {
            return $cb($this->entityRepository);
        } catch ( \Exception $e ) {
            throw self::translateException($e);
        }
    }

    /**
     * Turns a Doctrine exception into one in the Xyster exception hierarchy.
     *
     * @param \Exception $e The exception to translate
     * @return \Xyster\Dao\Exception The exception to use
     */
    public static function translateException(\Exception $e)
    {
        if ( $e instanceof \Doctrine\ORM\EntityNotFoundException ||
                $e instanceof \Doctrine\ORM\NoResultException ||
                $e instanceof \Doctrine\ORM\NonUniqueResultException ) {
            return new \Xyster\Dao\DataRetrievalException($e->getMessage(), $e->getCode(), $e);
        } else if ( $e instanceof \Doctrine\ORM\PessimisticLockException ||
                $e instanceof \Doctrine\ORM\OptimisticLockException ) {
            return new \Xyster\Dao\ConcurrencyException($e->getMessage(), $e->getCode(), $e);
        } else if ( $e instanceof \Doctrine\ORM\Query\QueryException ) {
            return new \Xyster\Dao\InvalidResourceUsageException($e->getMessage(), $e->getCode(), $e);
        } else {
            return new DoctrineSystemException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
