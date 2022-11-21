<?php
    // session_start();

    require_once '../db.php';

    if(!empty($_POST["postId"]) && !empty($_POST["rating"])) { 
        $postId = $_POST["postId"]; 
        $rating = $_POST["rating"];

        $userIP = $_SERVER["REMOTE_ADDR"];
    }

    

    

?>