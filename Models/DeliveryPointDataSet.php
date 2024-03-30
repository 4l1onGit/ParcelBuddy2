<?php
require_once('Database.php'); //Used for the Database connection
require_once('DeliveryPoint.php'); //Used to create DeliveryPoint Objects when storing data

class DeliveryPointDataSet //Used for handling DeliveryPoint data
{
    protected $_dbHandle, $_dbInstance;

    public function __construct()
    { //Establish Connection to database
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllDeliveries() : array
    {
        $sqlQuery = 'SELECT * FROM delivery_point INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new DeliveryPoint($row);
        }
        return $dataSet; //Returns the records (An array containing DeliverPoint objects)
    }



    //Create delivery points
    public function createDeliveryPoint($data) : bool
    {
        $sqlQuery = 'INSERT INTO delivery_point (Name, AddressOne, AddressTwo, Postcode, Deliverer, Lat, Lng, Status) VALUES (?, ?, ?, ?, ?, ?, ?, 1)'; //SQL query stored in variable
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $data['name']);  //Binds the data to parameter (?) prevents SQL injection
        $statement->bindParam(2, $data['addressOne']);
        $statement->bindParam(3, $data['addressTwo']);
        $statement->bindParam(4, $data['postcode']);
        $statement->bindParam(5, $data['deliverer']);
        $statement->bindParam(6, $data['lat']);
        $statement->bindParam(7, $data['lng']);
        return $statement->execute(); //Executes the PDO statement and returns bool to confirm success
    }

    //Read delivery points 
    public function fetchUserDeliveries($id) : array
    {
        $sqlQuery = 'SELECT * FROM delivery_point INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code WHERE delivery_point.Deliverer = ?';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $id);
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new DeliveryPoint($row);
        }
        return $dataSet;
    }
    //Update delivery points 
    public function updateDeliveryPoint($data) : bool
    {
        $paramCount = 0;
        $params = [];
        try {
            $sqlQuery = 'UPDATE delivery_point SET '; //Prepare Sql query

            if (isset($data['name'])) { //Adds parameters to query based on the values that will be updated
                $sqlQuery .= 'Name = ?,';
                $params[$paramCount] = $data['name'];
                $paramCount++;
            }
            if (isset($data['addressOne'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'AddressOne = ?,';
                $params[$paramCount] = $data['addressOne'];
                $paramCount++;
            }
            if (isset($data['addressTwo'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'AddressTwo = ?,';
                $params[$paramCount] = $data['addressTwo'];
                $paramCount++;
            }
            if (isset($data['postcode'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Postcode = ?,';
                $params[$paramCount] = $data['postcode'];
                $paramCount++;
            }
            if (isset($data['deliverer'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Deliverer = ?,';
                $params[$paramCount] = $data['deliverer'];
                $paramCount++;
            }
            if (isset($data['lat'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Lat = ?,';
                $params[$paramCount] = $data['lat'];
                $paramCount++;
            }
            if (isset($data['lng'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Lng = ?,';
                $params[$paramCount] = $data['lng'];
                $paramCount++;
            }
            if (isset($data['status'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Status = ?,';
                $params[$paramCount] = $data['status'];
                $paramCount++;
            }
//            if (isset($data['del_photo']) ) { To be implemented
//                if($paramCount > 0) {
//                    $sqlQuery.=' ';
//                }
//                $sqlQuery .= 'Del_Photo = ?,';
//                $params[$paramCount] = $data['del_photo'];
//                $paramCount++;
//            }
            if($paramCount > 0) {
                $sqlQuery = rtrim($sqlQuery, ',');
            }

            $sqlQuery .= ' WHERE delivery_point.ID = ?';
            $statement = $this->_dbHandle->prepare($sqlQuery);


            for( $i = 0; $i < $paramCount; $i++) { //Loops through parameters, uses bind to prevent sql injection
                $statement->bindValue($i + 1, $params[$i]);
            }
            $statement->bindValue(++$paramCount, $data['id']);
            return $statement->execute();
        } catch (Exception $ex) {
            return false;
        }
    }



    //Delete update points
    public function deleteDeliveryPoint($id) : bool
    {
        $sqlQuery = 'DELETE FROM delivery_point WHERE delivery_point.id = ?'; //SQL query stored in variable
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $id); //Binds the data to parameter (?) prevents SQL injection
        return $statement->execute(); // execute the PDO statement and returns boolean value to confirm success
    }



    public function fetchSearchDeliveries(string $q) : array {
        $q = "$q%";
        $sqlQuery = 'SELECT * FROM delivery_point  INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code WHERE delivery_point.Name LIKE ?'; //prepare Sql query
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindValue(1, $q);
        $statement->execute();
        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new DeliveryPoint($row);
        }
        return $dataSet;
    }

    public function fetchFilteredDeliveries(array $data, int $id = null) : array
    {

        $paramCount = 0;
        $params = [];
        try {
            $sqlQuery = 'SELECT * FROM delivery_point  INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code WHERE '; //prepare Sql query

            if (isset($data['id'])) { //Applies parameters to query based on the filters applied
                $sqlQuery .= 'ID = ?';
                $params[$paramCount] = $data['id'];
                $paramCount++;
            }
            if (isset($data['name'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'Name LIKE ?';

                $params[$paramCount] = $data['name'];
                $paramCount++;
            }
            if (isset($data['addressOne'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'AddressOne = ?';

                $params[$paramCount] = $data['addressOne'];
                $paramCount++;
            }
            if (isset($data['addressTwo'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'AddressTwo = ?';

                $params[$paramCount] = $data['addressTwo'];
                $paramCount++;
            }
            if (isset($data['postcode'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'Postcode = ?';

                $params[$paramCount] = $data['postcode'];
                $paramCount++;
            }

            if (isset($data['lat'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'FORMAT(Lat, 2) = FORMAT(?, 2)';

                $params[$paramCount] = $data['lat'];
                $paramCount++;
            }
            if (isset($data['lng'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'FORMAT(Lng, 2) = FORMAT(?, 2)';

                $params[$paramCount] = $data['lng'];
                $paramCount++;
            }
            if (isset($data['status'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'status_code = ?';
                $params[$paramCount] = $data['status'];
                $paramCount++;
            }

            if($paramCount > 0) {
                $sqlQuery = rtrim($sqlQuery, 'OR ');
            }

            if ($id !== null || isset($data['deliverer'])) { //Used to filter deliveries that are associated to the selected user
                if ($paramCount > 0) {
                    $sqlQuery .= ' AND ';
                }
                $sqlQuery .= 'Deliverer = ?';

                $params[$paramCount] = $data['deliverer'] ?? $id;
                $paramCount++;
                str_replace('OR', 'AND', $sqlQuery);
            }

            $statement = $this->_dbHandle->prepare($sqlQuery);


           for( $i = 0; $i < $paramCount; $i++) { //Loops through the amount of parameters set to bind values to prevent SQL injection
                $statement->bindValue($i + 1, $params[$i]);
            }

            $statement->execute();
            $dataSet = [];
          while ($row = $statement->fetch()) {
                $dataSet[] = new DeliveryPoint($row); //Creates delivery point objects
            }


            return $dataSet; //Returns the delivery points

        } catch (Exception $ex) {
            return [];
        }

    }

}