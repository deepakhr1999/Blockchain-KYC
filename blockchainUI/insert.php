<?php
$servername = "localhost";
$username = "phpmyadmin";
$password = "password";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$result = $conn->query($sql);
$err = 0;
if($result == false){
	$err = $conn->error;
}
$conn->close();

?>