<?php
  session_start();

  if (!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== TRUE) {
      header("location: login.php");
      exit;
  }

    require_once '../db.php';
    require_once '../models/post.php';
    require_once '../models/user.php';
    require_once '../functions/getParamsFromUrl.php';

    $titleEdited = $excerptEdited = $textEdited= "";
    $title_err = $excerpt_err = $text_err = $image_err = $gallery_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($_POST["title"]))) {
            $title_err = "Has to contain text";
        } else if (strlen(trim($_POST["title"])) > 100) {
            $title_err = "Too long";
        } else {
            $titleEdited = trim($_POST["title"]);
        }

        // if (empty(trim($_POST["excerpt"]))) {
        //     $excerpt_err = "Has to contain text";
        // } else if (strlen(trim($_POST["excerpt"])) > 100) {
        //     $excerpt_err = "Too long";
        // } else {
        //     $excerptEdited = trim($_POST["excerpt"]);
        // }

        if (empty(trim($_POST["text"]))) {
            $text_err = "Has to contain text";
        } else if (strlen(trim($_POST["text"])) > 300) {
            $text_err = "Too long";
        } else {
            $textEdited = trim($_POST["text"]);
        }
        // if ($post->userOwnsThisPost)

        if (empty($title_err) && empty($excerpt_err) && empty($text_err)) {
            $sql = "UPDATE posts SET title = ?, excerpt = ?, text = ? WHERE id = ?";

            $stmt = $dbConnection->stmt_init();

            if ($stmt->prepare($sql)) {
                $stmt->bind_param("sssi", $param_title, $param_excerpt, $param_text, $param_postEditId);

                $param_title = $titleEdited;
                $param_excerpt = $excerptEdited;
              
                $param_text = $textEdited;

                $param_postEditId = (int)$_SESSION["post-id"];
                // var_dump($param_postEditId);
                // die();
                if ($stmt->execute()) {
                    $stmt->close();
                    $userId = $_SESSION["id"];
                    $_SESSION["post-id"] = NULL;
                    header("location: profile.php?user-id=".$_SESSION["id"]);
                } else {
                    echo "Something went kaboom";
                }
            }
            $stmt->close();
        }
    } else {
        $postId = getParamsFromUrl("post-id");
        $_SESSION["post-id"] = $postId;
        $post = new Post($dbConnection);
        
        if ($post->getOne($postId) === FALSE) {
            header("location: index.php");
            exit;
        }
        
        $userOwnsThisPost = $post->userOwnsThisPost($_SESSION["id"]);
        $titleEdited = $post->getTitle();
        $textEdited = $post->getText();
        $post_user_id = $post->getUserId($_SESSION["id"]);
        
        $user = new User($dbConnection);
        
        if ($user->getOne($post_user_id) === FALSE) {
            header("location: index.php");
            exit;
        }
        
        $postOwnerUsername = $user->getUsername();

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
    <title>CREATE NEW POST</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/custom.scss">
    <link rel="stylesheet" href="../assets/style/navbar.scss">

</head>
<body>
    <header class="navbar">
        <div class="navbar-text" style="position: absolute; top: 40px; left: 20px;">
            <h2 class="navbar- text-white">EDIT POST</h2>
        </div>
        <div class="navbar-btn" style="position: absolute;  bottom: 10px; left: 10px;">
            <a class="btn btn-outline-warning" href="profile.php?user-id=<?= $profileUserId ?>">View profile</a>
        </div>
    </header>
    <div class="main-wrapper" style="padding: 100px; max-width: 1000px;">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Thumnail of your whip</label>
                <input type="file" name="thumbnail" class="form-control" value=""/>
                <span class="invalid-feedback"><?php echo $image_err; ?></span>
            </div>
            <div class="form-group">
                <label>Pictures of your whip</label>
                <input type="file" name="gallery[]" class="form-control" multiple value=""/>
                <span class="invalid-feedback"><?php echo $gallery_err; ?></span>
            </div>
            <div class="form-group">
                <label>Make</label>
                <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? "is-invalid" : ''; ?>" value="<?php echo $titleEdited; ?>">
                <span class="invalid-feedback"><?php echo $title_err; ?></span>
            </div>
            <!-- <div class="form-group">
                <label>Excerpt</label>
                <input type="text" name="excerpt" class="form-control <?php echo (!empty($excerpt_err)) ? "is-invalid" : ''; ?>" value="<?php echo $excerptEdited; ?>">
                <span class="invalid-feedback"><?php echo $excerpt_err; ?></span>
            </div> -->
            <div class="form-group">
                <label>Short description</label>
                <textarea style="min-height: 300px;" type="text" name="text" class="form-control <?php echo (!empty($text_err)) ? "is-invalid" : ''; ?>">
                <?= $textEdited ?>
                </textarea>
                <span class="invalid-feedback"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-outline-warning" value="submit">
            </div>
        </form>
        </div>
    </div>
</body>
</html>