<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'header.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $suggestion = test_input($_POST["suggestion"]);
    $id = $_SESSION['id'];
    $sql = "INSERT INTO suggestions VALUES (NULL, NULL, '$suggestion', $id)";
    $conn = new mysqli('localhost', 'wizard', 'Wizard_Smart-cities4', 'Hogwarts');
    if ($conn->query($sql) === TRUE) {
        redirect("suggestions.php");
    } else {
        echo("Error: " . $sql . "<br>" . $conn->error);
        redirect("welcomeWizard.php");
    }
}
else{
	redirect("login.html"); // Login fallido
}

function test_input($data){
	$data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); 
	return filter_var($data, FILTER_SANITIZE_STRING); // http://php.net/manual/es/filter.filters.sanitize.php
}
?>
    