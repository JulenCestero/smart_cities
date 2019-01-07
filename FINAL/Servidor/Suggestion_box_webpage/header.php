<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet" id="bootstrap_header-css">
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<?php
/* Comprobación de login correcto */
session_start();
$conn = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
$sql = "SELECT password FROM users WHERE username = '" . $_SESSION['username'] . "'";
$result = $conn->query($sql);
if($result->num_rows == 1){
    $passwordRow = $result->fetch_assoc();
    $myHashPass = $passwordRow['password'];
    if($myHashPass != $_SESSION['password']){
        unset($_SESSION['username']);
        unset($_SESSION['password']);
        unset($_SESSION['id']);
        unset($_SESSION['privileges']);
        redirect("login.html"); // Login fallido
    }
}
else{
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['id']);
    unset($_SESSION['privileges']);
    redirect("login.html"); // Login fallido
}

/* *** FUNCTIONS *** */
function redirect($url) {
  ob_start();
  header('Location: '. $url);
  ob_end_flush();
  die();
}
?>

<div class="container">
    <div class="row">
        <div class="span12">
            <div class="head">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="span6" bgcolor="#FF0000">
                            <h1 class="text-success">Donostyle Management</h1>
                        </div>

                        <div class="span4 offset2" style="margin-top:15px;">
                            <button class="btn pull-right" type="button" onclick="window.location.href='logout.php'">Cerrar
                                sesión</button>
                        </div>
                    </div>
                </div>

                <div class="navbar">
                    <div class="navbar-inner">
                        <div class="container">
                            <ul class="nav">
                                <li>
                                    <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        echo('<a href="hub_admin.php">Hub principal</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        echo('<a href="hub_guide.php">Hub principal</a>');
                                    }
                                    else{
                                        unset($_SESSION['username']);
                                        unset($_SESSION['password']);
                                        unset($_SESSION['privileges']);
                                        unset($_SESSION['id']);
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        echo('<a href="guides.php">Guías</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        echo('<a href="finantial_guide.php">Finanzas</a>');
                                    }
                                    else{
                                        unset($_SESSION['username']);
                                        unset($_SESSION['password']);
                                        unset($_SESSION['privileges']);
                                        unset($_SESSION['id']);
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        echo('<a href="create_new_tour.php">Nuevo Tour</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        //echo('<a href="finantial_guide.php">Finanzas</a>');
                                    }
                                    else{
                                        unset($_SESSION['username']);
                                        unset($_SESSION['password']);
                                        unset($_SESSION['privileges']);
                                        unset($_SESSION['id']);
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <a href="#">Ayuda</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>