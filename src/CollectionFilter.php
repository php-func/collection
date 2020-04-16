<?php 

class CollectionFilter extends Collection
{
    public function __construct(array $collection = null)
    {
        parent::__construct($collection);
    }

    /**
     * eine neue Sammlung des Objekten mit Attribute Werten '$value' erstellen
     * entfernt Elemente, die einen bestimmten Attributwert '$value' nicht haben.
     *
     */
    public function filterBy($attribute, $value)
    {
        $this->rewind();
        while ($this->valid()) {
            $collection_attribute = new ObjectAttribute($this->current(), $attribute);
            if ($collection_attribute->getCurrentAttributeValue() == $value) {
                $this->removeCurrent();
            }
            $this->next();
        }
        $this->rewind();

        return $this;
    }
}
