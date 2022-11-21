<?php

require_once '../functions/convertToArray.php';

class Post {

    private $id;
    private $dbConnection;
    private $title;
    private $excerpt;
    private $text;
    private $post_owner_id;
    private $publish_date;
    private $is_deleted;
    private $image;
    private $gallery;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getOne($postId)
    {
        $this->id = (int)$postId;

        $sql = "SELECT title, excerpt, text, user_id, publish_date, image, gallery, is_deleted FROM posts WHERE id = ?";
        $stmt = $this->dbConnection->stmt_init();

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("i", $param_postId);
            $param_postId = $this->id;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($db_title, $db_excerpt, $db_text, $db_post_owner_id, $db_publish_date, $db_image, $db_gallery, $db_is_deleted);
                    $stmt->fetch();

                    $this->title = $db_title;
                    $this->excerpt = $db_excerpt;
                    $this->text = $db_text;
                    $this->post_owner_id = (int)$db_post_owner_id;
                    $this->publish_date = $db_publish_date;
                    $this->image = $db_image;
                    $this->gallery = convertToArray($db_gallery);
                    $this->is_deleted = $db_is_deleted;

                } else {
                    return FALSE;
                }
            }
        }
        $stmt->close();
    }

    public function getAllFromUser($userId)
    
    {
        // var_dump($userId);

        $sql = "SELECT * FROM posts WHERE user_id = ? AND NOT is_deleted";
        $stmt = $this->dbConnection->stmt_init();
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("i", $param_userId);
            $param_userId = $userId;
            if ($stmt->execute()) {
                // var_dump("executed");
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }

    public function userOwnsThisPost($user_id)
    {
        if ((int)$user_id === $this->post_owner_id) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getUserId()
    {
        return $this->post_owner_id;
    }

    public function getPublishDate()
    {
        return $this->publish_date;
    }

    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    public function getImageName()
    {
        return $this->image;
    }

    public function getGalleryImages()
    {
        return $this->gallery;
    }

}


?>