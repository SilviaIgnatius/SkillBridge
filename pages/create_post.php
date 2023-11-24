<?php

require_once '../classes/DbConnector.php';
require_once '../classes/Post.php';

session_start();

if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $description = $_POST["post"];
        $created_at = date("Y-m-d H:i:s");

        // Handle image upload
        $image_path = handleFileUpload('image_path', 'uploads/images/');

        // Handle video upload
        $video_path = handleFileUpload('video_path', 'uploads/videos/');

        // Create a Post object and save it to the database
        $post = new \classes\Post($userId, $description, $created_at, $image_path, $video_path);

        // Get the database connection
        $con = \classes\DbConnector::getConnection();

        // Call the createPost method which internally handles file uploads
        $post->createPost($con);

        // Redirect to the postFeed.php page after post creation
        header("Location: postFeed.php");
        exit();
    } else {
        // Handle the case when the form is not submitted
        echo "Form not submitted!";
    }
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: ./Home.php");
    exit();
}

function handleFileUpload($fileInputName, $targetDirectory) {
    $targetDir = realpath(_DIR_ . 'uploads') . $targetDirectory;
    $uploadedFile = $_FILES[$fileInputName];


    if (!empty($uploadedFile['tmp_name'])) {
        $fileType = pathinfo($uploadedFile["name"], PATHINFO_EXTENSION);

        // Convert the file extension to lowercase
        $fileType = strtolower($fileType);

        // Generate a unique filename
        $fileName = uniqid() . '.' . $fileType;
        $targetFilePath = $targetDir . '/' . $fileName;

        // Check if the target directory exists; if not, create it
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Check file size and type
        if ($uploadedFile["size"] > 0 && in_array($fileType, ["jpg", "jpeg", "png", "gif", "mp4"])) {
            // Move the uploaded file to the destination
            if (move_uploaded_file($uploadedFile["tmp_name"], $targetFilePath)) {
                return $targetFilePath;
            } else {
                echo "Move uploaded file failed!";
            }
        } else {
            echo "Invalid file type or size! File type: $fileType, File size: {$uploadedFile['size']}";
        }
    }

    return null; 
}