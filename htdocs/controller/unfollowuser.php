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

    $userToUnfollow = new User($dbConnection);
    $userToUnfollow->getOne($profileUserId);

    $loggedInUser = new User($dbConnection);
    $loggedInUser->getOne((int)$_SESSION["id"]);

    if(!in_array($userToUnfollow->getId(),$loggedInUser->getFollowing()) || (string)$userToUnfollow->getId() === (string)$loggedInUser->getId()) {
        if ($redirectTo == 'profile') {
            header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToUnfollow->getId());
        } else {
            header("location: ../view/".$redirectTo.".php");
        }
        exit;
    }

    $userToUnfollow->removeUserToList($loggedInUser->getId(),'followers');

    $loggedInUser->removeUserToList($userToUnfollow->getId(),'following');

    header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToUnfollow->getId());

?>