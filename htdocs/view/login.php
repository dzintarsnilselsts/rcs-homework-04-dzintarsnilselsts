<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE) {
    header("location: index.php");
    exit;
}

require_once "../db.php";

$usernameOrEmail = $password = "";
$usernameOrEmail_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["usernameOrEmail"]))) {
        $usernameOrEmail_err = "Please enter your username";
    } else {
        $usernameOrEmail = trim($_POST["usernameOrEmail"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($usernameOrEmail_err) && empty($password_err)) {
        $sql = "SELECT id, username, email, password FROM users WHERE username = ? OR email = ?";

        $stmt = $dbConnection->stmt_init();
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("ss", $param_usernameOrEmail, $param_usernameOrEmail);

            $param_usernameOrEmail = $usernameOrEmail;

            if($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $email, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                        session_start();

                        $_SESSION["loggedin"] = TRUE;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;

                        header("location: index.php");
                        } else {
                            $login_err = "Invalid username, email or password";
                        }
                    }
                } else {
                    $login_err = "Invalid username, email or password";
                }
            }
            $stmt->close();
        }
    }
}


$sql = "SELECT * FROM posts WHERE NOT is_deleted = 1";

$stmt = $dbConnection->prepare($sql);
if ($stmt->execute()) {
    $allPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Something went kaboom";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <div class="navbar-btn" style="position: absolute; bottom: 10px; left: 10px;">
            <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) { ?>
                <!-- <a class="btn btn-outline-warning"  href="login.php">Login</a>
                <a class="btn btn-outline-warning" href="register.php">Register</a> -->
            <?php } else { ?>
                <a class="btn btn-outline-warning" href="../controller/logout.php">Sign out</a>
                <a class="btn btn-outline-warning" href="profile.php?user-id=<?= $profileUserId ?>">View profile</a>
            <?php } ?>
        </div>
   
    <div class="login-container">
        <?php
        if(!empty($login_err)) {
            echo '<div class="alert alert-login">' . $login_err . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="col-sm-20 my-2"> 
            <div class="form-group">
                <h2 class="navbar- text-dark" >Login</h2>
                <label>Username or Email</label>
                <input type="text" name="usernameOrEmail" class="form-control <?php echo (!empty($usernameOrEmail_err)) ? "is-invalid" : ""; ?>" value="<?php echo $usernameOrEmail; ?>">
                <span class="invalid-feedback"><?php echo $usernameOrEmail_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? "is-invalid" : ""; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-outline-dark" value="login">
            </div>
            <P>Press here to <a class="btn btn-outline-dark" style="margin: 10px" href="register.php">Sign up</a></P>
        </form>
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