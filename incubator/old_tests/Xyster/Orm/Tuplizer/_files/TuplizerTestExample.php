<?php
class TuplizerTestExample
{
    protected $_id;
    protected $_name;
    protected $_age;
    protected $_gender;
    protected $_component;
    protected $_version;
    
    protected $_parent;
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function setId( $id )
    {
        $this->_id = $id;
    }
    
    public function getVersion()
    {
        return $this->_version;
    }
    
    public function setVersion( $version )
    {
        $this->_version = $version;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function setName( $name )
    {
        $this->_name = $name;
    }
    
    public function setAge( $age )
    {
        $this->_age = $age;
    }
    public function getAge()
    {
        return $this->_age;
    }
    
    public function getGender()
    {
        return $this->_gender;
    }
    public function setGender( $gender )
    {
        $this->_gender = $gender;
    }
    
    public function setParent( $parent )
    {
        $this->_parent = $parent;
    }
    public function getParent()
    {
        return $this->_parent;
    }
    
    public function getComponent()
    {
        return $this->_component;
    }
    public function setComponent( TuplizerTestExampleComponent $component )
    {
        $this->_component = $component;
    }
}

class TuplizerTestExampleComponent
{
    protected $_address;
    protected $_city;
    protected $_zip;
    
    public function getAddress()
    {
        return $this->_address;
    }
    
    public function getCity()
    {
        return $this->_city;
    }
    
    public function getZip()
    {
        return $this->_zip;
    }
    
    public function setZip($zip)
    {
        $this->_zip = $zip;
    }
    public function setAddress($address)
    {
        $this->_address = $address;
    }
    public function setCity($city)
    {
        $this->_city = $city;
    }
}