<?php

// upload.php

if(isset($_POST["image"]))
{
    $data = $_POST["image"];

    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);

    $data = base64_decode($data);

    $filename = uniqid() . '.png';

    $filepath = "../uploads/" . $filename;

    file_put_contents($filepath, $data);

    $conn = new mysqli("localhost", "root", "", "skillbridge");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userId = 3; // Change this to the desired user ID
    $updateQuery = "UPDATE user SET profilepic = '$filepath' WHERE userid = $userId";

    if ($conn->query($updateQuery) === TRUE) {
        echo $filepath; // Return the new image path for display
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
}
?>
