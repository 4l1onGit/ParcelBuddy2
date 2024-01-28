<?php
class DeliveryStatusType extends UserData //Inherits userdata fields
{
    protected $_id, $_statusCode, $_statusText;

    public function __construct($dbRow)
    {
        $this->_id = $dbRow['StatusID']; //Fields store db rows
        $this->_statusCode = $dbRow['status_code'];
        $this->_statusText = $dbRow['status_text'];
    }

    public function getStatusCode() //Getters
    {
        return $this->_statusCode;
    }

    public function getStatusText()
    {
        return $this->_statusText;
    }

}