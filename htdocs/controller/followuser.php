<?php

    session_start();

    if (!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] != TRUE) {
        header("location: ../view/index.php");
        exit;
    }

    require_once '../db.php';
    require_once '../functions/getParamsFromUrl.php';
    require_once '../models/user.php';

    $profileUserId = getParamsFromUrl("user-id");
    $redirectTo = getParamsFromUrl("redirect");

    $userToFollow = new User($dbConnection);
    $userToFollow->getOne($profileUserId);

    $loggedInUser = new User($dbConnection);
    $loggedInUser->getOne((int)$_SESSION["id"]);

    if(in_array($userToFollow->getId(),$loggedInUser->getFollowing()) || (string)$userToFollow->getId() === (string)$loggedInUser->getId()) {
        if ($redirectTo == 'profile') {
            header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToFollow->getId());
        } else {
            header("location: ../view/".$redirectTo.".php");
        }
        exit;
    }

    $userToFollow->addUserToList($loggedInUser->getId(),'followers');

    $loggedInUser->addUserToList($userToFollow->getId(),'following');

    header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToFollow->getId());

?>