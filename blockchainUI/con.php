<?php 
	function call($sql){
		$servername = "localhost";
		$username = "phpmyadmin";
		$password = "password";
		$conn = new mysqli($servername, $username, $password);
		$result = $conn->query($sql) or die($conn->error);
		// Check connection
		// if ($conn->connect_error) {
		//     die("Connection failed: " . $conn->connect_error);
		// } 

		// $result = $conn->query($sql);
		// $err = 0;
		// if($result == false){
		// 	$err = $conn->error;
		// }
		$conn->close();
		return $result;
	}
 ?>