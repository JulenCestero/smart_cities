<?php
session_start();
$_SESSION = array();
session_destroy();
redirect("login.html");
function redirect($url){
    ob_start();
    header('Location: ' . $url);
    ob_end_flush();
    die();
}
?>