<?php

namespace classes;

use PDO;
use PDOException;

class Post {

    private $post_id;
    private $userid;
    private $description;
    private $created_at;
    private $image_path;
    private $video_path;
    private $like_count;

    public function __construct($userid, $description, $created_at, $image_path = null, $video_path = null) {
        $this->userid = $userid;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->image_path = $image_path;
        $this->video_path = $video_path;
        $this->like_count = 0; // Initialize like count to zero
    }

    public function setPostId($post_id) {
        $this->post_id = $post_id;
    }

    function setImage_path($image_path) {
        $this->image_path = $image_path;
    }

    function setVideo_path($video_path) {
        $this->video_path = $video_path;
    }

    public function getPostId() {
        return $this->post_id;
    }

    public function getUserId() {
        return $this->userid;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getImagePath() {
        return $this->image_path;
    }

    public function getVideoPath() {
        return $this->video_path;
    }

    public function getLikeCount() {
        return $this->like_count;
    }

    public function createPost($con) {
        try {
            $query = "INSERT INTO Posts (userid, description, created_at, image_path, video_path, likes) VALUES (?, ?, ?, ?, ?, ?)";
            $pstmt = $con->prepare($query);
            $pstmt->bindValue(1, $this->userid);
            $pstmt->bindValue(2, $this->description);
            $pstmt->bindValue(3, $this->created_at);
            $pstmt->bindValue(4, $this->image_path); // Set the image path
            $pstmt->bindValue(5, $this->video_path); // Set the video path
            $pstmt->bindValue(6, $this->like_count);
            $pstmt->execute();

            // Retrieve the auto-generated post_id
            $this->post_id = $con->lastInsertId();

            return ($pstmt->rowCount() > 0);
        } catch (PDOException $exc) {
            die("Error in Post class's createPost function: " . $exc->getMessage());
        }
    }

    // Example method in Post class
    public static function retrieveUserPosts($con, $userId) {
        // Modify your SQL query to fetch posts only for the specified user
        $query = "SELECT * FROM posts WHERE userid = ?";
        $pstmt = $con->prepare($query);
        $pstmt->bindValue(1, $userId);
        $pstmt->execute();

        // Fetch posts and return them
        return $pstmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addLike($con) {
        try {
            $this->like_count++;
            $query = "UPDATE Posts SET likes = ? WHERE post_id = ?";
            $pstmt = $con->prepare($query);
            $pstmt->bindValue(1, $this->like_count);
            $pstmt->bindValue(2, $this->post_id);
            $pstmt->execute();
            return ($pstmt->rowCount() > 0);
        } catch (PDOException $exc) {
            die("Error in Post class's addLike function: " . $exc->getMessage());
        }
    }

    public function removeLike($con) {
        try {
            if ($this->like_count > 0) {
                $this->like_count--;
                $query = "UPDATE Posts SET likes = ? WHERE post_id = ?";
                $pstmt = $con->prepare($query);
                $pstmt->bindValue(1, $this->like_count);
                $pstmt->bindValue(2, $this->post_id);
                $pstmt->execute();
                return ($pstmt->rowCount() > 0);
            } else {
                return false;
            }
        } catch (PDOException $exc) {
            die("Error in Post class's removeLike function: " . $exc->getMessage());
        }
    }

    public function toggleLike($con, $postId) {
        try {
            // Fetch the current likes count from the database
            $query = "SELECT likes FROM Posts WHERE post_id = ?";
            $pstmt = $con->prepare($query);
            $pstmt->bindValue(1, $postId);
            $pstmt->execute();
            $result = $pstmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $currentLikes = $result['likes'];

                // Toggle likes (for simplicity, just increment/decrement by 1)
                $newLikes = ($currentLikes == 0) ? 1 : 0;

                // Update the 'Posts' table with the new likes count
                $query = "UPDATE Posts SET likes = ? WHERE post_id = ?";
                $pstmt = $con->prepare($query);
                $pstmt->bindValue(1, $newLikes);
                $pstmt->bindValue(2, $postId);
                $pstmt->execute();

                // Return true on success
                return true;
            }
        } catch (PDOException $exc) {
            // Handle any exceptions
            // You can log the error, return false, or take appropriate action
            return false;
        }

        // Return false if the script reaches here (something went wrong)
        return false;
    }

}
