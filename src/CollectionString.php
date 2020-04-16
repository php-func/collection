class CollectionString extends Collection
{
    public function __construct(array $collection = null)
    {
        $this->setType('string');
        parent::__construct($collection);
    }

    public function add($string)
    {
        return !empty($string) && $this->addToCollection($string);
    }

    public function getCollectionAsString($separator = ', ')
    {
        if ($this->isEmpty()) {
            return null;
        }
        return implode($separator, $this->getCollection());
    }
}
