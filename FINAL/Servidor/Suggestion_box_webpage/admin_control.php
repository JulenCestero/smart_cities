<?php
        $conn = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    $sql = "SELECT privileges FROM users WHERE id = '" . $_SESSION['id'] . "'";
    $result = $conn->query($sql);
    if($result->num_rows == 1){
        if ($_SESSION['privileges'] != 1){ // Queda comprobar si esto es correcto
            session_destroy();
            redirect('login.html');
            die();
        }
    } else {
        session_destroy();
        redirect('login.html');
        die();
    }
?>

