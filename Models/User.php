<?php

class User
{
    protected $_id, $_username, $_authenticated, $_userType; //Class used to store authenticated user details to be accessed throughout the application e.g. to check usertype
    public function __construct()
    {
        $this->_id = '';
        $this->_username = '';
        $this->_authenticated = false;
        $this->_userType = '';
    }

    public function getId() //Accessors and mutators to allow user to set fields (when logging in) and to access fields e.g. Username
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function setUsername($username)
    {
        $this->_username = $username;
    }

    public function getAuthenticated()
    {
        return $this->_authenticated;
    }

    public function setAuthenticated($authenticated)
    {
        $this->_authenticated = $authenticated;
    }

    public function getUserType()
    {
        return $this->_userType;
    }
    public function setUserType($userType)
    {
        $this->_userType = $userType;
    }



}