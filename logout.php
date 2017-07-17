<?php
session_start();



if (isset($_SESSION['loggedin'])) {
    session_destroy();
    session_unset();
    unset($_SESSION['uname']);
    header ("Location: index.php");
}

if (isset($_SERVER['REQUEST_URI'])){
    $prev_page = $_SERVER['HTTP_REFERER'];
    header ("Location: $prev_page");
} else{
    header ("Location: index.php");
}

