<?php
    $conn = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    $sql = "SELECT privileges FROM Users WHERE username = '" . $_SESSION['Username'] . "'";
    $result = $conn->query($sql);
    if($result->num_rows == 1){
        if ($_SESSION['Privileges'] != 0){ // Queda comprobar si esto es correcto
            $_SESSION = array();
            session_destroy();
            echo "holu";
            redirect("login.html");
        }
    } else {
        $_SESSION = array();
        session_destroy();
        redirect("login.html");
    }
?>

