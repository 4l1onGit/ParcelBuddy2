<?php

class UserType
{
    protected $_id, $_statusCode; //Stores usertypes

    public function __construct($dbRow)
    {
        $this->_id = $dbRow['id'];
        $this->_statusCode = $dbRow['status_code'];

    }

    public function getStatusID() //Accessors for usertype id and statuscode
    {
        return $this->_id;
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }

}