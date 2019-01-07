<?php
session_start();
$name = $password = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$user = test_input($_POST["user"]);
	$password = hash("sha256", $_POST["password"]);
	$conn = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
	if($conn->connect_error){
		die("Connection failed: " . $conn->connect_error);
	}
	$conn->set_charset("utf8");
	$sql = "SELECT password, id FROM usuarios WHERE username='" . $user . "'";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		$passwordRow = $result->fetch_assoc();
		$myHashPass  = $passwordRow['Password'];
		if($myHashPass == $password){
			$_SESSION['username'] = $user;
			$_SESSION['password'] = $myHashPass;
			$_SESSION['id']	      = $passwordRow['id'];
			if($_SESSION['id'] == 2) redirect("welcomeDumbeldore.php");
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
