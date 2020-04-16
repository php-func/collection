<?php

class CollectionUniqueValidator extends CollectionValidator
{
    public function __construct(ObjectValidatable $validator, array $collection = null)
    {
        parent::__construct($validator, $collection);
    }

    public function add($data)
    {
        /** @var ObjectValidatable $validator */
        $validator = $this->getValidator();
        $validator->setData($data);
        return $validator->isValid($data) && $this->isUniqueId($data) && parent::add($data);
    }

    /**
     * don't duplicate in Collection
     *
     */
    public function isUniqueId($object)
    {
        $collection_attribute = new ObjectAttribute($object, 'id');
        return $this->isUniqueAttribute($collection_attribute);
    }

    protected function isUniqueAttribute(ObjectAttribute $object_attribute)
    {
        $attribute_value = $object_attribute->getCurrentAttributeValue();
        return !$this->isExistAttributeValue($object_attribute, $attribute_value);
    }
}
