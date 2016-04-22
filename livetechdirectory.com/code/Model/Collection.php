<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Phpld_Model_Collection implements  Iterator, Countable, ArrayAccess
{

    protected $_collection = array();
    protected $_entityClass = null;

    /**
     * Rows number without LIMIT statement applied
     *
     * @var int
     */
    protected $_countWithoutLimit = 0;

    public function __construct($entityClass, $countWithoutLimit = 0)
    {
        $this->_countWithoutLimit = $countWithoutLimit;
        $this->_entityClass = $entityClass;
    }

    public function setElements($elements) {
        switch (true) {
            case is_array($elements):
                $this->_collection = $elements;
                break;

            default:
                throw new Exception('Unsupported collection elements param was given');
                break;
        }
        return $this;
    }

    /**
     * Get raw collection
     *
     * @return array
     */
    public function getRawCollection()
    {
        return $this->_collection;
    }

    /**
     * Returns array representation of collection
     *
     */
    public function toArray()
    {
        $data = array();
        foreach ($this->_collection as $row) {
            $row = $this->create($row);
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Return the current element
     *
     */
    public function current()
    {
        // receive current record
        $entity = current($this->_collection);
        // transfer the record to object
        if (!is_object($entity)) {
            if (is_array($entity)) {
                $className = $this->getClassNameFor($entity);
                $entity = $this->create($entity, $className);
            } else {
                return false;
            }
            $this->setCurrent($entity);
        }

        return $entity;
    }


    /**
     * Count of rows found before paging the results
     *
     * @return int
     */
    public function count()
    {
        return count($this->_collection);
    }

    /**
     * Return the key of the current element
     *
     * @return scalar scalar on success, integer 0 on failure.
     */
    public function key()
    {
        return key($this->_collection);
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        return next($this->_collection);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->_collection);
    }

    /**
     * Check whether there is more entries
     *
     * @return boolean
     */
    public function valid()
    {
        return (bool) $this->current();
    }

    /**
     * Checks if offset exists
     *
     * @param mixed $offset offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_collection[$offset]);
    }

    /**
     * Returns the value at given offset
     *
     * @param mixed $offset offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->_collection[$offset];
    }

    /**
     * Set the value at given offset
     *
     * @param mixed $offset offset
     * @param mixed $value  value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset) {
            $this->_collection[$offset] = $value;
        } else {
            $this->_collection[] = $value;
        }
    }

    /**
     * Unsets the element at given offset
     *
     * @param mixed $offset offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_collection[$offset]);
    }

    /**
     * Replace current record with this object
     *
     * @param object $entity object to store on place of originaly received record
     *
     * @return void
     */
    public function setCurrent($entity)
    {
        $this->_collection[$this->key()] = $entity;
    }

    /**
     * Detect class name for entity base on its data
     *
     * @param array $entityData data
     *
     * @return string or FALSE if not detected
     */
    public function getClassNameFor($entityData)
    {
        if (!is_null($this->_entityClass)) {
            return $this->_entityClass;
        }

        return false;
    }

    /**
     * Create instance of contact
     *
     * @param array|Members_Model_Contact $data      data to populate entity with
     * @param string                      $className name of the class
     *
     * @return object
     */
    public function create($data, $className=null)
    {
        if (is_null($className)) {
            $className = $this->getClassNameFor($data);
            if (!$className) {
                // TODO throw better exception
                throw new Exception(
                    "you have to provide \$className parameter or idclass field as part of \$data parameter"
                );
            }
        }

        if (method_exists($className, "factory")) {
            $obj =  call_user_func($className.'::factory', $data);
        } else {
            $obj = new $className($data);
        }

        return $obj;
    }

    public function setCountWithoutLimit($countWithoutLimit)
    {
        $this->_countWithoutLimit = $countWithoutLimit;
    }

    public function countWithoutLimit()
    {
        return $this->_countWithoutLimit;
    }


    /**
     * Append element to collection
     *
     * @param $element
     * @return MKLib_Model_Collection
     */
    public function append($element)
    {
        array_push($this->_collection, $element);
        return $this;
    }

    /**
     * Prepend element to collection
     *
     * @param $element
     * @return MKLib_Model_Collection
     */
    public function prepend($element)
    {
        array_unshift($this->_collection, $element);
        return $this;
    }

    /**
     * Merge collections
     *
     * @param MKLib_Model_Collection $collection
     * @return MKLib_Model_Collection
     */
    public function merge($collection)
    {
        $this->_merge($collection);
        return $this;
    }

    /**
     * Merge collections
     *
     * @param MKLib_Model_Collection $collection
     * @return MKLib_Model_Collection
     */
    protected function _merge($collection)
    {
        $this->_collection = array_merge($this->_collection, $collection->getRawCollection());
    }
}