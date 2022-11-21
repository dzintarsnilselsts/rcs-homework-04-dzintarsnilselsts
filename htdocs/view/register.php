<?php
require_once "../db.php";

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers and underscores";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";

        $stmt = $dbConnection->stmt_init();

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s", $param_username);

            $param_username = trim($_POST["username"]);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows() == 1) {
                    $username_err = "This username is already taken";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Something went kaboom";
            }
        }
    }

    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password";
    } elseif (strlen(trim($_POST["password"])) <6) {
        $password_err = "Password needs to be atleast 6 characters";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Sorry, password doesn't match";
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";

        $stmt = $dbConnection->stmt_init();
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s", $param_email);

            $param_email = trim($_POST["email"]);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $email_err = "This email has already been used";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Something went kaboom. Try again later";
            }
            $stmt->close();
        }
    }

    if(empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, email, password) VALUES (?,?,?)";

        $stmt = $dbConnection->stmt_init();
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("ssss", $param_username, $param_email, $param_password, $param_confirm_password);

            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_confirm_password = password_hash($confirm_password, PASSWORD_DEFAULT);

            if($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Something went kaboom";
            }
        }
        $stmt->close();
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
    <title>Register</title>
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
    <div class="register-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="width: inherit">
            <div class="form-group" style="margin: 5px">
                <input type="text" placeholder="username" class="form-control <?php echo (!empty($username_err)) ? "is-invalid" : ""; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group" style="margin: 5px">
                <input type="text" placeholder="email@example.com" class="form-control <?php echo (!empty($email_err)) ? "is-invalid" : ""; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group" style="margin: 5px">
                <input type="password" placeholder="password" class="form-control <?php echo (!empty($password_err)) ? "is-invalid" : ""; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group" style="margin: 5px">
                <input type="password" placeholder="confirm password" class="form-control <?php echo (!empty($confirm_password_err)) ? "is-invalid" : ""; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
    
            <div class="form-group" style="margin: 5px">
                <input type="submit" class="btn btn-outline-dark" value="Submit">
                <input type="reset" class="btn btn-outline-dark" value="Reset">
            </div>
        </form>
    </div>
        <div class="container" style="display: flex; margin: 30px; width: 150px">
            <h2>Sign Up</h2>
            <p>Have an account?<a href="login.php">Press here to Login</a></p>
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