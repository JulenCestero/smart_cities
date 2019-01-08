<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet" id="bootstrap_header-css">
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<?php
/* Comprobación de login correcto */
session_start();

if($_SESSION['logged'] != 1){
    session_destroy();
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
                            <h1 class="text-success" style="color:#FFFFFF;">Hogwarts services</h1>
                        </div>

                        <div class="span4 offset2" style="margin-top:15px;">
                            <button class="btn pull-right" type="button" onclick="window.location.href='logout.php'">Logout</button>
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
                                        echo('<a href="welcomeWizard.php">Home</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        echo('<a href="welcomeAdmin.php">Home</a>');
                                    }
                                    else{
                                        session_destroy();
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        //echo('<a href="guides.php">Guías</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        //echo('<a href="finantial_guide.php">Finanzas</a>');
                                    }
                                    else{
                                        session_destroy();
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <a href="#">Help</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>