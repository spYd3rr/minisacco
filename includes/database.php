<?php

session_start();

/*
 * this file initializes our dataase variables
 * these database variables are used in our forms to resister users and also
 * to log the to our web applications
 * we will use PDO_MYSQL for our database
 */

// we first create a PHP function called 'dbconnect' that handles/contains our database environment variables
// this function returns a handle calle '$connection' that connects to the database
function dbconnect(){
    try{
        // try and catch are used to handle any errors safely
        $db_host   = 'localhost';
        $db_user   = 'root';
        $dbname    = 'sharina';
        $db_pass   = '';

        $connection = new PDO("mysql:host=$db_host;dbname=$dbname;",$db_user, $db_pass);
        // set PDO error mode to exception so as to echo any
        $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    }
    catch (PDOException $e){
        echo 'Connection to the database failed!'.$e->getMessage();

    }
    if (!empty($connection)) {
        return $connection;
    }
    return $connection;
}





$db_host   = 'localhost';
$db_user   = 'root';
$dbname    = 'sharina';
$db_pass   = '';

$connection = new PDO("mysql:host=$db_host;dbname=$dbname;",$db_user, $db_pass);
// set PDO error mode to exception so as to echo any
$connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);





/*
 * include all the files for database and password here
 * so that we do not send multiple headers to server
 */

/*
 * this is a password hashing class
 * that we will use to encrypt passwords to the database and vise versa
 * it is open source
 */
include_once 'Bcrypt.php';

/**
 * this file calls our user_defined errors to show to the user
 */
include_once "sharina_messages.php";

/**
 * we call our "functions.php" page before our class
 */
include_once "functions.php";

/**
 * we call our main class here
 */

include_once "Sharina.php";