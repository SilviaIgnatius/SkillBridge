<?php
// toggle_like.php

// Assuming you have a database connection established
require_once 'classes/DbConnector.php';
require_once 'classes/Post.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['postId'])) {
    $postId = $_GET['postId'];

    // Assuming you have a Post class with a method to toggle likes
    $con = \classes\DbConnector::getConnection();
    $post = new \classes\Post();

    $success = $post->toggleLike($con, $postId);

    if ($success) {
        $updatedLikes = $post->getLikeCount(); // Fetch the updated likes count
        echo json_encode(['success' => true, 'likes' => $updatedLikes]);
        exit();
    } else {
        // Log the error for debugging
        error_log('Failed to toggle like for postId: ' . $postId);
    }
}

// If the script reaches here, something went wrong
echo json_encode(['success' => false]);
?>
