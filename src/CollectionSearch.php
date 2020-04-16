<?php

abstract class CollectionSearch extends Collection
{
    /**
     * is existing value of attribute in objects collection
     *
     */
    public function isExistAttributeValue(ObjectAttribute $object_attribute, $needle)
    {
        if ($this->isEmpty()) {
            return null;
        }
        $validator = new ObjectValidator($object_attribute);
        if (!$validator->isValidAttribute()) {
            return null;
        }
        if (empty($needle)) {
            return null;
        }
        if (is_numeric($needle)) {
            $needle = (string)$needle;
        }

        if (is_array($needle) && count($needle) > 0) {
            $array_needle = $needle;
        } else if (is_string($needle)) {
            $array_needle[] = $needle;
        } else {
            return null;
        }

        $this->rewind();

        while ($this->valid()) {
            // set Current Object
            $object_attribute->setObject($this->current());
            // get Current Value From Attribute Value
            $current_attribute_value = $object_attribute->getCurrentAttributeValue();
            if ($this->compareValues($current_attribute_value, $array_needle)) {
                return true;
            }
            $this->next();
        }
        $this->rewind();

        return false;
    }

    /**
     * find occurrences value (string) the parameter of object in collection
     *
     */
    public function findValueByAttribute($attribute, $needle)
    {
        if ($this->isEmpty()) {
            return null;
        }
        if (!is_string($attribute) || strlen($attribute) < 1) {
            return null;
        }
        if (is_numeric($needle)) {
            $needle = (string)$needle;
        }

        if (is_array($needle) && count($needle) > 0) {
            $array_needle = $needle;
        } else if (is_string($needle) && strlen($needle) > 0) {
            $array_needle[] = $needle;
        } else {
            return null;
        }

        $new_collection = $this->newCollection();

        $this->rewind();
        while ($this->valid()) {
            $collection_attribute    = new ObjectAttribute($this->current(), $attribute);
            $current_attribute_value = $collection_attribute->getCurrentAttributeValue();
            if ($this->compareValues($current_attribute_value, $array_needle)) {
                $new_collection->add($this->current());
            }
            $this->next();
        }
        $this->rewind();

        if ($new_collection->isEmpty()) {
            return null;
        }
        return $new_collection;
    }

    public function compareValues($current_attribute_value, array $array_needle)
    {
        if (empty($current_attribute_value)) {
            return null;
        }
        if (!is_array($array_needle)) {
            return null;
        }
        if (is_numeric($current_attribute_value)) {
            $current_attribute_value = (string)$current_attribute_value;
        }

        foreach ($array_needle as $needle) {
            if ($current_attribute_value == $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * find occurrences id of object in collection
     *
     */
    public function findValueById($id)
    {
        $new_collection = $this->findValueByAttribute('id', $id);
        return $new_collection;
    }
}
