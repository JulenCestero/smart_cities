<?php
session_start();
$name = $password = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$user = hash("sha256", test_input($_POST["user"])); // NEEDS SANETIZATION
	$password = hash("sha256", test_input($_POST["password"])); // NEEDS SANETIZATION
	$conn = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
	if($conn->connect_error){
		die("Connection failed: " . $conn->connect_error);
	}
	$conn->set_charset("utf8");
	$sql = "SELECT password, id, privileges FROM users WHERE username='" . $user . "'";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		$passwordRow = $result->fetch_assoc();
		$myHashPass  = $passwordRow['password'];
		if($myHashPass == $password){
			$_SESSION['logged'] = 1;
			$_SESSION['id']	      = $passwordRow['id'];
			$_SESSION['privileges'] = $passwordRow['privileges'];
			if($_SESSION['id'] == 1) redirect("welcomeDumbeldore.php");
			else redirect("welcomeWizard.php");
		}
	}
	redirect("login.html"); // Login fallido
}
else{
	redirect("login.html"); // Login fallido
}

function redirect($url){
	ob_start();
	header('Location: ' . $url);
	ob_end_flush();
	die();
}
function test_input($data){
	$data = trim($data);
	$data = stripslashes($data);
	return htmlspecialchars($data);
}
?>
