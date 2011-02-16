<?php

class Application_Model_Post
{

    protected $_userid;
		protected $_username;
    protected $_created;
    protected $_post;
    protected $_id;
 
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid post property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid post property');
        }
        return $this->$method();
    }
 
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
 
    public function setUserid($userid)
    {
        $this->_userid = (string) $userid;
        return $this;
    }
 
    public function getUserid()
    {
        return $this->_userid;
    }
		
    public function setUsername($username)
    {
        $this->_username = (string) $username;
        return $this;
    }
 
    public function getUsername()
    {
				$selecteduser = new Application_Model_UserMapper();
        return $selecteduser->usernameById($this->getUserid());
    }
 
    public function setPost($post)
    {
        $this->_post = (string) $post;
        return $this;
    }
 
    public function getPost()
    {
        return $this->_post;
    }
 
    public function setCreated($ts)
    {
        $this->_created = $ts;
        return $this;
    }
 
    public function getCreated()
    {
        return $this->_created;
    }
 
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
    }

}

