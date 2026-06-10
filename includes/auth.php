<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: /hotel_mini/login.php");
    exit();
}

$timeout = 7200; // 2 tiếng

if(isset($_SESSION['last_activity'])){

    if(time() - $_SESSION['last_activity'] > $timeout){

        session_unset();
        session_destroy();

        header("Location: /hotel_mini/login.php?timeout=1");
        exit();
    }
}

$_SESSION['last_activity'] = time();
?>