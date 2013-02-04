<?php

namespace LemoBase\Mapper;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractMapper
{
    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var object
     */
    protected $entityPrototype;

    /**
     * @var HydratingResultSet
     */
    protected $resultSetPrototype;

    /**
     * @var Select
     */
    protected $selectPrototype;

    /**
     * @var Sql
     */
    protected $sql = null;

    /**
     * @var string
     */
    protected $table;

    /**
     *
     * @var integer
     */
    protected $lastInsertValue = null;

    /**
     * Constructor
     *
     * @param string $table
     * @param Adapter $adapter
     */
    public function __construct($table, Adapter $adapter)
    {
        $this->table = $table;
        $this->adapter = $adapter;

        // Sql object (factory for select, insert, update, delete)
        $this->sql = new Sql($this->adapter, $this->table);
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return Sql
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * select
     *
     * @return Select
     */
    protected function select()
    {
        if (!$this->selectPrototype) {
            $this->selectPrototype = new Select;
        }

        return clone $this->selectPrototype;
    }

    /**
     * @param Select $select
     * @return HydratingResultSet
     */
    public function selectWith(Select $select, $entityPrototype = null, HydratorInterface $hydrator = null)
    {
        $adapter = $this->getAdapter();
        $statement = $adapter->createStatement();
        $select->prepareStatement($adapter, $statement);
        $result = $statement->execute();

        $resultSet = $this->getResultSet();

        if (isset($entityPrototype)) {
            $resultSet->setObjectPrototype($entityPrototype);
        }

        if (isset($hydrator)) {
            $resultSet->setHydrator($hydrator);
        }

        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * @param object|array $entity
     * @param array $fieldsToUnset
     * @return ResultInterface
     */
    protected function insert($entity, array $fieldsToUnset = array())
    {
        $rowData = $this->entityToArray($entity);
        foreach($fieldsToUnset as $field) {
            unset($rowData[$field]);
        }
        foreach ($rowData as $k => $v) {
            if($v instanceof \DateTime) {
                $rowData[$k] = $v->format('Y-m-d H:i:s');
            }
        }

        $insert = $this->getSql()->insert();
        $insert->values($rowData);

        $this->lastInsertValue = $this->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();

        $statement = $this->getSql()->prepareStatementForSqlObject($insert);
        return $statement->execute();
    }

    /**
     * @param object|array $entity
     * @param  string|array|closure $where
     * @param array $fieldsToUnset
     * @return mixed
     */
    protected function update($entity, $where, array $fieldsToUnset = array())
    {
        $rowData = $this->entityToArray($entity, $fieldsToUnset);
        foreach($fieldsToUnset as $field) {
            unset($rowData[$field]);
        }
        foreach ($rowData as $k => $v) {
            if($v instanceof \DateTime) {
                $rowData[$k] = $v->format('Y-m-d H:i:s');
            }
        }

        $update = $this->getSql()->update();
        $update->set($rowData);
        $update->where($where);
        $statement = $this->getSql()->prepareStatementForSqlObject($update);
        $result = $statement->execute();

        return $result->getAffectedRows();
    }

    protected function delete(Where $where = null)
    {
        $delete = $this->getSql()->delete();
        $delete->where($where);

        $result = $this->getSql()->prepareStatementForSqlObject($delete)->execute();

        return $result->getAffectedRows();
    }

    /**
     * Execute given SQL query
     *
     * @param string $query
     * @return \Zend\Db\Adapter\Driver\StatementInterface|\Zend\Db\ResultSet\ResultSet
     */
    protected function executeQuery($query)
    {
        return $this->getAdapter()->query($query, Adapter::QUERY_MODE_EXECUTE);
    }

    /**
     * @return object
     */
    public function getEntityPrototype()
    {
        return $this->entityPrototype;
    }

    /**
     * @param object $modelPrototype
     * @return AbstractDbMapper
     */
    public function setEntityPrototype($entityPrototype)
    {
        $this->entityPrototype = $entityPrototype;
        $this->resultSetPrototype = null;
        return $this;
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ClassMethods(false);
        }
        return $this->hydrator;
    }

    /**
     * @param HydratorInterface $hydrator
     * @return AbstractDbMapper
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        $this->resultSetPrototype = null;
        return $this;
    }

    /**
     * @return HydratingResultSet
     */
    protected function getResultSet()
    {
        if (!$this->resultSetPrototype) {
            $this->resultSetPrototype = new HydratingResultSet;
            $this->resultSetPrototype->setHydrator($this->getHydrator());
            $this->resultSetPrototype->setObjectPrototype($this->getEntityPrototype());
        }
        return clone $this->resultSetPrototype;
    }

    /**
     * Uses the hydrator to convert the entity to an array.
     *
     * Use this method to ensure that you're working with an array.
     *
     * @param object $entity
     * @return array
     */
    protected function entityToArray($entity)
    {
        if (is_array($entity)) {
            return $entity; // cut down on duplicate code
        } elseif (is_object($entity)) {
            return $this->getHydrator()->extract($entity);
        }
        throw new \Exception('Entity passed to db mapper should be an array or object.');
    }

    /**
     * Get last insert value
     *
     * @return integer
     */
    public function getLastInsertValue()
    {
        return $this->lastInsertValue;
    }
}
