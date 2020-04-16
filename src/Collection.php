<?php

abstract class Collection extends ObjectType implements Iterator
{
    /** @var array of objects */
    protected $collection;

    /** @var string last_operation: del/add/next/rewind - für set correct key after CREATE and REMOVED */
    private $_last_operation;

    public function __construct(array $collection = null)
    {
        if (!is_null($collection)) {
            $this->setCollection($collection);
        }
    }

    /**
     * ELEMENT: add new with defined type of object
     * Add object with is defined in child implementation
     *
     */
    public function add($object)
    {
        return $this->addToCollection($object);
    }

    /**
     * REMOVE ALL ELEMENTS
     */
    public function resetCollection()
    {
        $this->collection = [];
        $this->rewind();
        return $this;
    }

    /**
     * COLLECTION: summary for many collections
     * nur hinzufugen neue elemente für existiert
     * Add new elements by convert to collection and after valid in loop hin zufügen
     
     */
    public function addCollection($array_collection)
    {
        if (is_null($array_collection)) {
            return null;
        }
        $new_collection = null;
        if ($array_collection instanceof Collection) {
            $new_collection = $array_collection;
        } else if (is_array($array_collection)) {
            $new_collection = $this->newCollection();
            $new_collection->setCollection($array_collection);
            $new_collection->rewind();
        }

        while ($new_collection->valid()) {
            foreach ($array_collection as $object) {
                $this->add($object);
            }
            $new_collection->next();
        }

        return $this;
    }

    public function newCollection()
    {
        $new_collection = clone $this;
        $new_collection->resetCollection();
        return $new_collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }
    
    public function isCollectionValid()
    {
        return $this->isArrayValid($this->getCollection());
    }


    public function isArrayValid($array)
    {
        if (!is_array($array)) {
            return false;
        }
        if (count($array) < 1) {
            return false;
        }
        return true;
    }

    /**
     * COLLECTION: set new
     * create neue Collection, wenn nur SUMMARY dann mit addCollection method
     *
     * @param array $collection
     *
     * @return $this|bool
     */
    public function setCollection(array $collection)
    {
        if (!$this->isArrayValid($collection)) {
            return null;
        }

        $this->resetCollection();
        foreach ($collection as $object) {
            $this->add($object);
        }

        $this->rewind();
        return $this;
    }

    protected function addToCollection($item)
    {
        $this->_last_operation = 'add';
        $this->setTypeByObject($item);
        if ($this->isValidType($item)) {
            $this->collection[] = $item;
            return true;
        }
        return false;
    }

    public function removeCurrent()
    {
        $this->_last_operation = 'del';
        if (is_null($this->current())) {
            return false;
        }
        try {
            unset($this->collection[$this->key()]);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * ELEMENT_POSITION: next
     */
    public function next()
    {
        if ($this->_last_operation == 'del') {
            $this->_last_operation = 'next';
            return false;
        }
        $this->_last_operation = 'next';
        return is_array($this->collection) && next($this->collection);
    }

    /**
     * ELEMENT_POSITION: reset
     */
    public function rewind()
    {
        $this->_last_operation = 'rewind';
        return is_array($this->collection) && reset($this->collection);
    }

    /**
     * ELEMENT_POSITION: name of key
     *
     * @return int
     */
    public function key()
    {
        return key($this->collection);
    }

    /**
     * ELEMENT: current
     *
     * @return mixed
     */
    public function current()
    {
        if (!$this->isCurrentKeyValid()) {
            return null;
        }
        return $this->collection[$this->key()];
    }

    /**
     * COLLECTION: count all elements
     */
    public function size()
    {
        return count($this->getCollection());
    }

    /**
     * COLLECTION: IS EMPTY
     */
    public function isEmpty()
    {
        return !$this->isArrayValid($this->getCollection());
    }

    /**
     * element is ready to read
     */
    public function valid()
    {
        return $this->isCurrentKeyValid();
    }

    public function isCurrentKeyValid()
    {
        if (!is_array($this->getCollection())) {
            return false;
        }
        if (is_null($this->key())) {
            return false;
        }

        if (!array_key_exists($this->key(), $this->getCollection())) {
            return false;
        }
        return true;
    }

    public function isCurrentValueValid()
    {
        return !is_null($this->current());
    }

    public function toArray()
    {
        return $this->getCollection();
    }
}
