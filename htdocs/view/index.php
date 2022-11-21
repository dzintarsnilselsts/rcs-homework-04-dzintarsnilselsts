<?php
session_start();

require_once '../db.php';
require_once '../controller/ratepost.php';


$sql = "SELECT * FROM posts WHERE NOT is_deleted = 1";

$stmt = $dbConnection->prepare($sql);
if ($stmt->execute()) {
    $allPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Something went kaboom";
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE) {
$sql = "SELECT id FROM users WHERE id = ?";
$stmt = $dbConnection->stmt_init();

    if ($stmt->prepare($sql)) {
        $stmt->bind_param("i", $profileUserId);
        $profileUserId = $_SESSION["id"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate My Whip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/custom.scss">
    <link rel="stylesheet" href="../assets/style/navbar.scss">

</head>
<body>
    <header class="navbar">
        <div class="navbar-text" style="position: absolute; top: 40px; left: 20px;">
            <h1 class="navbar- text-white">WELCOME TO RATE MY WHIP</h1>
            <p class="navbar- text-white">"Whip" - A bad ass car that you just bought. <i>"Here's the new whip"</i></p>
        </div>
        <div class="navbar-btn" style="position: absolute;  bottom: 10px; left: 10px;">
            <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) { ?>
                <a class="btn btn-outline-warning" href="login.php">Login</a>
                <a class="btn btn-outline-warning" href="register.php">Register</a>
            <?php } else { ?>
                <a class="btn btn-outline-warning" href="../controller/logout.php">Sign out</a>
                <a class="btn btn-outline-warning" href="profile.php?user-id=<?= $profileUserId ?>">View profile</a>
            <?php } ?>
        </div>
    </header> 
    <div class="main-wrapper" style="margin-left: 80px; padding: 60px; display: flex; flex-direction: column-reverse; align-items: flex-start;">
        <?php foreach ($allPosts as $post) { ?>
            <a href="post.php?post-id=<?php echo $post["id"]; ?>">
                <h3 class="text-dark text-uppercase"><?php echo $post["title"]; ?></h3>
                <p class="text-secondary text-uppercase"><?php echo $post["excerpt"]; ?></p>
                <div class="rating">
                    <p class="text-dark text-uppercase">Rate this whip</p>
                    <input id="rim6" name="rim" type="image" src="../assets/pageimages/icon6.png" value="6" class="rim6" />
                    <input id="rim5" name="rim" type="image" src="../assets/pageimages/icon5.png" value="5" class="rim5" />
                    <input id="rim4" name="rim" type="image" src="../assets/pageimages/icon4.png" value="4" class="rim4" />
                    <input id="rim3" name="rim" type="image" src="../assets/pageimages/icon3.png" value="3" class="rim3" />
                    <input id="rim2" name="rim" type="image" src="../assets/pageimages/icon2.png" value="2" class="rim2" />
                    <input id="rim1" name="rim" type="image" src="../assets/pageimages/icon1.png" value="1" class="rim1" />
                </div>
                <div class="gallery-wrapper" style="padding: 0px 0px 30px">
                    <img src="../assets/images/<?php echo $post["image"] ?>" alt="" height="350" width="auto" style="border-radius: 20px; margin: 10px">
                </div>
            </a>
            <hr>
        <?php } ?>
    </div>
</body>
</html>