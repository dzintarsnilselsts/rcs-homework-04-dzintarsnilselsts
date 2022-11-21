<?php

    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
        header("location: login.php");
        exit;
    }

    require_once '../db.php';

    $title = $excerpt = $text = $image = $gallery = "";
    $title_err = $excerpt_err = $text_err = $image_err = $gallery_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($_POST["title"]))) {
            $title_err = "Has to contain text";
        } else if (strlen(trim($_POST["title"])) > 100) {
            $title_err = "Too long";
        } else {
            $title = trim($_POST["title"]);
        }

        if (empty(trim($_POST["excerpt"]))) {
            $excerpt_err = "Has to contain text";
        } else if (strlen(trim($_POST["excerpt"])) > 100) {
            $excerpt_err = "Too long";
        } else {
            $excerpt = trim($_POST["excerpt"]);
        }

        if (empty(trim($_POST["text"]))) {
            $text_err = "Has to contain text";
        } else if (strlen(trim($_POST["text"])) > 300) {
            $text_err = "Too long";
        } else {
            $text = trim($_POST["text"]);
        }

        $tempname = $_FILES["thumbnail"]["tmp_name"];
        $filepath = tempnam('../assets/images', "");
        rename($filepath, $filepath .= '.png');
        unlink($filepath);
        $pathExploded = explode("\\", $filepath);
        $filename = $pathExploded[count($pathExploded)-1];

        if (!move_uploaded_file($tempname, $filepath)) {
            header("location: profile.php?user-id=".$_SESSION["id"]);
        }


        $galleryImages = $_FILES["gallery"];
        $galleryArray = reArrayFiles($galleryImages);
        $insertGalleryString = "[";

        
        foreach ($galleryArray as $key => $image) {
            $gallery_tempname = $image["tmp_name"];
            $gallery_filepath = tempnam('../assets/images', "");
            rename($gallery_filepath, $gallery_filepath .= '.png');
            unlink($gallery_filepath);
            $gallery_pathExploded = explode("\\", $gallery_filepath);
            $gallery_filename = $gallery_pathExploded[count($gallery_pathExploded)-1];

            $insertGalleryString = $insertGalleryString. "'".$gallery_filename."'";

            if ($key < count($galleryArray) -1 ) {
                $insertGalleryString = $insertGalleryString . ",";
            }

            if (!move_uploaded_file($gallery_tempname, $gallery_filepath)) {
                header("location: profile.php?user-id=".$_SESSION["id"]);
            }
        }

        $insertGalleryString = $insertGalleryString . "]";

        if (empty($title_err) && empty($excerpt_err) && empty($text_err)) {
            $sql = "INSERT INTO posts (title, excerpt, text, user_id, publish_date, image, gallery) VALUES (?,?,?,?,NOW(),?,?)";

            $stmt = $dbConnection->stmt_init();

            if ($stmt->prepare($sql)) {
                $stmt->bind_param("sssiss", $param_title, $param_excerpt, $param_text, $param_userId, $param_imageName, $param_gallery);
                $param_title = $title;
                $param_excerpt = $excerpt;
                $param_text = $text;
                $param_userId = $_SESSION["id"];
                $param_imageName = $filename;
                $param_gallery = $insertGalleryString;

                if ($stmt->execute()) {
                    header("location: profile.php?user-id=".$param_userId);
                } else {
                    echo "Something went kaboom";
                }
            }
            $stmt->close();
        }
    }

    function reArrayFiles($file_post) {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
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
            <h2 class="navbar- text-white">CREATE NEW POST</h2>
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
                <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? "is-invalid" : ''; ?>" value="<?php echo $title; ?>">
                <span class="invalid-feedback"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group">
                <label>Excerpt</label>
                <input type="text" name="excerpt" class="form-control <?php echo (!empty($excerpt_err)) ? "is-invalid" : ''; ?>" value="<?php echo $excerpt; ?>">
                <span class="invalid-feedback"><?php echo $excerpt_err; ?></span>
            </div>
            <div class="form-group">
                <label>Short description</label>
                <textarea style="min-height: 300px;" type="text" name="text" class="form-control <?php echo (!empty($text_err)) ? "is-invalid" : ''; ?>">
                <?= $text ?>
                </textarea>
                <span class="invalid-feedback"><?php echo $text_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-outline-warning" value="submit">
            </div>
        </form>
        </div>
    </div>
</body>
</html>