<?php
require_once('UserData.php');
require_once('DeliveryStatusType.php');
class DeliveryPoint extends DeliveryStatusType implements JsonSerializable//This class is a model for the delivery points
{
    protected $_id, $_name, $_addressOne, $_addressTwo, $_postcode, $_userId, $_lat, $_lng, $_status, $_delPhoto, $_statusText, $_statusCode;

    public function __construct($dbRow)
    {
        $this->_id = $dbRow['ID']; //Fields storing db rows
        $this->_name = $dbRow['Name'];
        $this->_addressOne = $dbRow['AddressOne'];
        $this->_addressTwo = $dbRow['AddressTwo'];
        $this->_postcode = $dbRow['Postcode'];
        $this->_userId = $dbRow['Deliverer'];
        $this->_lat = $dbRow['Lat'];
        $this->_lng = $dbRow['Lng'];
        $this->_status = $dbRow['Status'];
        $this->_delPhoto = $dbRow['Del_Photo'];
        $this->_username = $dbRow['username'];
        $this->_statusText = $dbRow['status_text'];
        $this->_statusCode = $dbRow['status_code'];
    }

    /**
     * @return int
     */
    public function getId() //Accessors functions to allow the access of fields in the views
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getAddressOne()
    {
        return $this->_addressOne;
    }

    /**
     * @return string
     */
    public function getAddressTwo()
    {
        return $this->_addressTwo;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->_postcode;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->_lat;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->_lng;
    }

    /**
     * @return string
     */
    public function getDelPhoto()
    {
        return $this->_delPhoto;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function jsonSerialize() : array
    {
       return ['name' => $this->_name];
    }
}