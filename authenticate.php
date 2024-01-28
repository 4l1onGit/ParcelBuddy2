<?php
require_once('Models/UserDataSet.php'); //Used for userdata
require_once('Models/User.php'); //Used to create authenticated User

session_start(); //Starts session

$view = new stdClass();

if (isset($_POST['loginbutton']) && isset($_POST['confirmLogin'])) { //Checks if a login attempt has been made
    $username = $_POST['username']; //Stores entered credentials
    $password = $_POST['password'];
    $userDataset = new UserDataSet(); //Creates User dataset object
    $users = $userDataset->fetchAllUsers(); //Uses function to retrieve all users
    foreach ($users as $userData) { //Checks if the users exists
        if ($userData->getUsername() == $username && ($userData->getPassword() == $password || password_verify($password, $userData->getPassword()))) { //Verifies user credentials
            $user = new User(); //Creates a user object
            $user->setId($userData->getUserID()); //Uses mutators to store values in User object
            $user->setUsername($userData->getUsername());
            $user->setAuthenticated(true);
            $user->setUserType($userData->getStatusCode());
            $_SESSION['login'] = $user; //Stores user object in session to allow access throughout application
            break;
        }
    }
}

if (isset($_POST["logoutbutton"])) { //Checks if the sign-out button has been used
    unset($_SESSION["login"]); //Removes user object
    session_destroy(); //Ends session
}

