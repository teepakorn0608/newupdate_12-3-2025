<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'mydb');


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comment_system";

// cpanel 
// define('DB_SERVER', 'localhost');
// define('DB_USERNAME', 'robinnarban');
// define('DB_PASSWORD', 'robin@5656');
// define('DB_NAME', 'robinnarban');
 
/* Attempt to connect to MySQL database */
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>