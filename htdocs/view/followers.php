<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== TRUE) {
        header("location: index.php");
        exit;
    }

    require_once '../db.php';

    $userId = $_SESSION["id"];

    $sql = "SELECT followers FROM users WHERE id = ?";

    $stmt = $dbConnection->stmt_init();
    if ($stmt->prepare($sql)) {
        $stmt->bind_param("i", $param_profileUserId);
        $param_profileUserId = (int)$userId;
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($followersFromDB);
                $stmt->fetch();

                $followers = str_replace(array('[',']'),'',$followersFromDB);
                $followers = str_replace("'",'',$followers);
                $followers = explode(",", $followers);
            }
        }
    }

    $sql = "SELECT * FROM users WHERE ";

    if (count($followers) > 0 && $followers[0] !== '') {
        foreach ($followers as $key => $value) {
            $sql = $sql . " id = " . $value;
            if ($key !== count($followers) -1) {
                $sql = $sql. " OR";
            }
        }

        $stmt = $dbConnection->stmt_init();
        if ($stmt->prepare($sql)) {
            if ($stmt->execute()) {
                $followersArray = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
        }
        $stmt->close();
    }

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
    <title>Following</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/custom.scss">
    <link rel="stylesheet" href="../assets/style/navbar.scss">

</head>
<body>
    <header class="navbar">
        <div class="navbar-text" style="position: absolute; top: 40px; left: 20px;">
            <h1 class="navbar- text-white">Following</h1>
        </div>
        <div class="navbar-btn" style="position: absolute;  bottom: 10px; left: 10px;">
            <a class="btn btn-outline-warning" href="index.php">Mainpage</a>
            <a class="btn btn-outline-warning" href="profile.php?user-id=<?= $profileUserId ?>">View profile</a>
        </div>
    </header>
    <div class="main-wrap">
        <?php if (isset($followersArray)) { ?>
            <?php foreach ($followersArray as $follower) { ?>
                <div style=" padding: 60px">
                    <p class="h1"><b style="text-transform:uppercase;"><?= $follower['username'] ?></b></p>
                    <a href="../controller/unfollowuser.php?user-id=<?= $follower['id'] ?>&redirect=profile">Unfollow</a>
                </div>
                <hr>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>