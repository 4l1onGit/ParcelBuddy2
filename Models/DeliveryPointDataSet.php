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

    public function fetchAllDeliveries(): array
    {
        $sqlQuery = 'SELECT * FROM delivery_point INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new DeliveryPoint($row);
        }
        return $dataSet; //Returns the records (An array containing DeliverPoint objects) now json encoded for ajax
    }



    //Create delivery points
    public function createDeliveryPoint(array $data) : bool
    {
        $sqlQuery = 'INSERT INTO delivery_point (Name, AddressOne, AddressTwo, Postcode, Deliverer, Lat, Lng, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'; //SQL query stored in variable
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $data['_name']);  //Binds the data to parameter (?) prevents SQL injection
        $statement->bindParam(2, $data['_addressOne']);
        $statement->bindParam(3, $data['_addressTwo']);
        $statement->bindParam(4, $data['_postcode']);

        $deliverer = intval($data['_deliverer']);
        $statement->bindParam(5,$deliverer);
        $statement->bindParam(6, $data['_lat']);
        $statement->bindParam(7, $data['_lng']);
        $status = intval($data['_status']);
        $statement->bindParam(8, $status);
        try {
            return $statement->execute(); //Executes the PDO statement and returns bool to confirm success
        } catch(PDOException $e) {
            return false;
        }

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

            if (isset($data['_name'])) { //Adds parameters to query based on the values that will be updated
                $sqlQuery .= 'Name = ?,';
                $params[$paramCount] = $data['_name'];
                $paramCount++;
            }
            if (isset($data['_addressOne'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'AddressOne = ?,';
                $params[$paramCount] = $data['_addressOne'];
                $paramCount++;
            }
            if (isset($data['_addressTwo'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'AddressTwo = ?,';
                $params[$paramCount] = $data['_addressTwo'];
                $paramCount++;
            }
            if (isset($data['_postcode'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Postcode = ?,';
                $params[$paramCount] = $data['_postcode'];
                $paramCount++;
            }
            if (isset($data['_deliverer'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Deliverer = ?,';
                $params[$paramCount] = $data['_deliverer'];
                $paramCount++;
            }
            if (isset($data['_lat'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Lat = ?,';
                $params[$paramCount] = $data['_lat'];
                $paramCount++;
            }
            if (isset($data['_lng'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Lng = ?,';
                $params[$paramCount] = $data['_lng'];
                $paramCount++;
            }
            if (isset($data['_status'])) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
                }
                $sqlQuery .= 'Status = ?,';
                $params[$paramCount] = $data['_status'];
                $paramCount++;
            }
            if (isset($data['del_photo']) ) {
                if($paramCount > 0) {
                    $sqlQuery.=' ';
               }
                $sqlQuery .= 'Del_Photo = ?,';
              $params[$paramCount] = $data['del_photo'];
               $paramCount++;
            }
            if($paramCount > 0) {
                $sqlQuery = rtrim($sqlQuery, ',');
            }

            $sqlQuery .= ' WHERE delivery_point.ID = ?';
            $statement = $this->_dbHandle->prepare($sqlQuery);


            for( $i = 0; $i < $paramCount; $i++) { //Loops through parameters, uses bind to prevent sql injection
                $statement->bindValue($i + 1, $params[$i]);
            }
            $statement->bindValue(++$paramCount, $data['_id']);

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



    public function fetchSearchDeliveries(string $q, $id) : array {
        $q = "$q%";
        if($id === '') {
            $sqlQuery = 'SELECT * FROM delivery_point  INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code WHERE delivery_point.Name LIKE ?'; //prepare Sql query
        } else {
            $sqlQuery = 'SELECT * FROM delivery_point  INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code WHERE delivery_point.Name LIKE ? AND delivery_point.Deliverer = ?'; //prepare Sql query
        }



        $statement = $this->_dbHandle->prepare($sqlQuery);
        if($id === '') {
            $statement->bindValue(1, $q);
        } else {
            $statement->bindValue(1, "%$q%");
            $statement->bindValue(2, $id);
        }
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
                $params[$paramCount] = intval($data['id']);
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
                $sqlQuery .= 'AddressOne LIKE ?';

                $params[$paramCount] = $data['addressOne'];
                $paramCount++;
            }
            if (isset($data['addressTwo'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'AddressTwo LIKE ?';

                $params[$paramCount] = $data['addressTwo'];
                $paramCount++;
            }
            if (isset($data['postcode'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'Postcode LIKE ?';

                $params[$paramCount] = $data['postcode'];
                $paramCount++;
            }

            if (isset($data['lat'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'FORMAT(Lat, 2) LIKE FORMAT(?, 2)';

                $params[$paramCount] = floatval($data['lat']);
                $paramCount++;
            }
            if (isset($data['lng'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' OR ';
                }
                $sqlQuery .= 'FORMAT(Lng, 2) LIKE FORMAT(?, 2)';

                $params[$paramCount] = floatval($data['lng']);
                $paramCount++;
            }
            if (isset($data['status'])) {
                if ($paramCount > 0) {
                    $sqlQuery .= ' AND ';
                }
                $sqlQuery .= 'status_code = ?';
                $params[$paramCount] = intval($data['status']);
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

                $params[$paramCount] = $id ?? intval($data['deliverer']);
                $paramCount++;
                str_replace('OR', 'AND', $sqlQuery);
            }

            $statement = $this->_dbHandle->prepare($sqlQuery);


           for( $i = 0; $i < $paramCount; $i++) { //Loops through the amount of parameters set to bind values to prevent SQL injection
               if(is_int($params[$i]) || is_float($params[$i])) {
                   $statement->bindValue($i+1, $params[$i]);
               } else {
                   $statement->bindValue($i + 1, '%'.$params[$i].'%');
               }
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

    public function fetchDelivery(int $id) : array|false
    {

        $sqlQuery = 'SELECT * FROM delivery_point  INNER JOIN delivery_users ON delivery_point.Deliverer = delivery_users.UserID INNER JOIN delivery_status ON delivery_point.Status = delivery_status.status_code WHERE delivery_point.ID = ?';
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $id);
        $dataSet = [];
        $statement->execute();
        try {
            while ($row = $statement->fetch()) {
                $dataSet[] = new DeliveryPoint($row); //Creates delivery point objects
            }

            return $dataSet; //Returns the delivery points
        } catch (Exception $ex) {

          return false;
        }

    }

}