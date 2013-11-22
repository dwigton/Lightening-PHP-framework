<?php
class Lightning_Model
{
    protected $data = array();
    protected $collection_type = "Lightning_Collection";
    protected $keys = array();
    protected $adapter;
    
    public function __construct() {
       // $this->adapter = new Lightning_Adapter;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function getValue($key)
    {
        if ($this->hasKey($key)) {
            return $this->data[$key];
        } else {
            // If the key id of the form foo.bar then check if bar will work.
            // this might be a bad idea.
            $parts = explode('.', $key);
            $key = array_pop($parts);
            if ($this->hasKey($key)) {
                return $this->data[$key];
            } else {
                return null;
            }
        }
    }
    
    public function hasKey($key)
    {
        return array_key_exists($key, $this->data);
    }
    
    public function setData($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        }
        return $this;
    }
    
    public function appendData($data)
    {
        if (is_array($data)) {
            $this->data = array_merge($this->data, $data);
        }
        return $this;
    }
    
    public function setValue($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function hasData()
    {
        return !empty($this->data);
    }
    
    public function setCollectionType($type)
    {
        $this->collection_type = $type;
        return $this;
    }
    
    public function getCollectionType()
    {
        return $this->collection_type;
    }
    
    public function getCollection()
    {
        $collection = new $this->collection_type;
        $collection->setItemType(get_class($this));
        
        return $collection;
    }
    
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }
    
    public function getAdapter()
    {
        return $this->adapter;
    }
}
