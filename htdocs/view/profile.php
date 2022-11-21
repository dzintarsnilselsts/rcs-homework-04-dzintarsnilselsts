<?php

    session_start();
    $guest = TRUE;
    $userOwnsProfile = FALSE;
    $isFollowingThisUser = FALSE;

    require_once '../db.php';
    require_once '../models/user.php';
    require_once '../models/post.php';
    require_once '../functions/getParamsFromUrl.php';
    
    $userId = getParamsFromUrl("user-id");
    $user = new User($dbConnection);
    $user->getOne($userId);

    $userOwnsProfile = $user->isOwner($_SESSION["id"]);
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE) {
        $guest = FALSE;
    } 

   

    $isFollowingThisUser = $user->isFollowingMe($_SESSION["id"]);
    
    $username = $user->getUsername();
    $email = $user->getEmail();
    $followers = $user->getFollowers();

    $allUserPosts = new Post($dbConnection);
    $allUserPosts = $allUserPosts->getAllFromUser($user->getId());
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/custom.scss">
    <link rel="stylesheet" href="../assets/style/navbar.scss">

</head>
<body>
    <header class="navbar">
        <div class="navbar-text" style="position: absolute; top: 40px; left: 20px;">
            <h2 class="navbar- text-white"><?php echo ($userOwnsProfile === TRUE) ? 'Welcome' : 'Viewing profile of' ?><b style="text-transform:uppercase;"> <?= $username ?></b></h2>
            <h2 class="navbar- text-white"><?php echo ($userOwnsProfile) ? 'All your posts' : 'All posts' ?></h2>
        </div>
        <div class="navbar-btn" style="position: absolute;  bottom: 10px; left: 10px;">
            <a class="btn btn-outline-warning" href="index.php">Homepage</a>
            <!-- <a class="btn btn-primary" href="explore.php">Explore</a> -->
            <?php if (!$guest && $userOwnsProfile) { ?>
                <a class="btn btn-outline-warning" href="following.php">Following</a>
                <a class="btn btn-outline-warning" href="followers.php">Followers</a>
                <button class="btn btn-outline-warning" style="cursor: progress;">Block User</button>
            <?php } ?>
            <?php if (!$guest && !$userOwnsProfile) { ?>
                <?php if (!$isFollowingThisUser) { ?>
                    <a class="btn btn-outline-warning" href="../controller/followuser.php?user-id=<?= $userId ?>&redirect=profile">Follow</a>
                <?php } else { ?>
                    <a class="btn btn-outline-warning" href="../controller/unfollowuser.php?user-id=<?= $userId ?>&redirect=profile">Unfollow</a>
                <?php } ?>
            <?php } ?>
        </div>
    </header>
    <?php if (!$guest && $userOwnsProfile) { ?>
        <a class="btn btn-warning"style="margin-left: 80px; margin-top: 20px" href="createpost.php">Create a New Post</a>
    <?php } ?>
    <div class="main-wrapper" style="margin-left: 80px; padding: 60px; display: flex; flex-direction: column-reverse; align-items: flex-start;">
        <?php foreach ($allUserPosts as $post) { ?>
            <a href="post.php?post-id=<?php echo $post["id"]; ?>">
                <h3 class="text-dark text-uppercase"><?php echo $post["title"]; ?></h3>
                <p class="text-secondary text-uppercase"><?php echo $post["excerpt"]; ?></p>
                <img src="../images/<?php echo $post["image"] ?>" alt="" height="200" width="auto" style="border-radius: 10px; margin: 10px">
            </a>
            <hr>
        <?php } ?>
    </div>
    <script src="../assets/style/bootstrap.js"></script>
</body>
</html>