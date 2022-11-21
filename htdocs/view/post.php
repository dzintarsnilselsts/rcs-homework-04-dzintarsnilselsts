<?php

require_once '../db.php';
require_once '../models/post.php';
require_once '../models/user.php';
require_once '../functions/getParamsFromUrl.php';

$postId = getParamsFromUrl("post-id");

$post = new Post($dbConnection);

if ($post->getOne($postId) === FALSE) {
    header("location: index.php");
    exit;
}

session_start();
$userOwnsThisPost = $post->userOwnsThisPost($_SESSION["id"]);
$title = $post->getTitle();
$text = $post->getText();
$post_user_id = $post->getUserId();
$publish_date = $post->getPublishDate();
$postImageName = $post->getImageName();
$galleryImages = $post->getGalleryImages();

$user = new User($dbConnection);

if ($user->getOne($post_user_id) === FALSE) {
    header("location: index.php");
    exit;
}

$postOwnerUsername = $user->getUsername();

$sql = "SELECT id FROM users WHERE id = ?";
$stmt = $dbConnection->stmt_init();

if ($stmt->prepare($sql)) {
    $stmt->bind_param("i", $profileUserId);
    $profileUserId = $_SESSION["id"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/custom.scss">
    <link rel="stylesheet" href="../assets/style/navbar.scss">
    
</head>
<body>
    <header class="navbar">
        <div class="navbar-text" style="position: absolute; top: 40px; left: 20px;">
            <h1 class="navbar- text-white">Posts</h1>
        </div>
        <div class="navbar-btn" style="position: absolute;  bottom: 10px; left: 10px;">
            <a class="btn btn-outline-warning" href="index.php">Mainpage</a>
            <a class="btn btn-outline-warning" href="profile.php?user-id=<?= $profileUserId ?>">View profile</a>
        </div>
    </header>
    <div class="main-wrapper" style="padding: 60px; display: flex; flex-direction: column; align-items: flex-start;">
        <h2 class="text-dark"><b><?= $title ?></b></h2>
        <?php if (isset($postOwnerUsername)) { ?>
            <a class="text-secondary" href="profile.php?user-id=<?= $post_user_id ?>"><h4><b><?= $postOwnerUsername ?></b></h4></a>
        <?php } ?>
        <p><?= $publish_date ?></p>
        <?php if ($userOwnsThisPost) { ?>
            <a class="btn btn-outline-warning btn-sm" style="margin: 5px;" href="editpost.php?post-id=<?= $postId ?>">Edit post</a>
        <?php } ?>
        <?php if ($userOwnsThisPost) { ?>
            <a class="btn btn-outline-warning btn-sm" style="margin: 5px;" href="../controller/deletepost.php?post-id=<?= $postId ?>">Delete post</a>
        <?php } ?>
        <div class="post-wrapper">
            <p class="text-large"><?= $text ?></p>
                <div class="image-wrapper">
                    <img style="border-radius: 20px; margin: 10px" src="../images/<?= $postImageName ?>" alt="" height="700px" width="auto">
                    <?php foreach ($galleryImages as $key => $imageName) { ?>
                    <img style="border-radius: 20px; margin: 10px" src="../images/<?= $imageName ?>" alt="" height="700px" width="auto">
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>