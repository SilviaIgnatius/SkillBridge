<?php
require_once '../classes/DbConnector.php';

use classes\DbConnector;

$dbcon = new DbConnector();

session_start();
if (isset($_SESSION["user_id"])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <link rel="stylesheet" href="../css/admin.css">
            <style>
                .addskill {
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    margin-bottom: 47px;
                    padding: 20px;
                    margin-top: 20px;

                }
            </style>
        </head>
        <body>

            <?php require '../include/adminNav.php'; ?>
            <div class="content">
                <button class="btn btn-danger logout-button">Logout</button>

                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "skillbridge";

                $conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $skillName = $_POST["skillName"];
                    $description = $_POST["description"];

                    $targetDir = "../uploads/";
                    $targetFile = $targetDir . basename($_FILES["skillImage"]["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                    if (isset($_POST["submit"])) {
                        $check = getimagesize($_FILES["skillImage"]["tmp_name"]);
                        if ($check !== false) {
                            echo "File is an image - " . $check["mime"] . ".";
                            $uploadOk = 1;
                        } else {
                            echo "File is not an image.";
                            $uploadOk = 0;
                        }
                    }

                    if (file_exists($targetFile)) {
                        echo "Sorry, file already exists.";
                        $uploadOk = 0;
                    }

                    if ($_FILES["skillImage"]["size"] > 500000) {
                        echo "Sorry, your file is too large.";
                        $uploadOk = 0;
                    }

                    $allowedExtensions = array("jpg", "jpeg", "png");
                    if (!in_array($imageFileType, $allowedExtensions)) {
                        echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
                        $uploadOk = 0;
                    }

                    if ($uploadOk == 0) {
                        echo "Sorry, your file was not uploaded.";
                    } else {
                        if (move_uploaded_file($_FILES["skillImage"]["tmp_name"], $targetFile)) {
                            echo "The file " . basename($_FILES["skillImage"]["name"]) . " has been uploaded.";

                            $stmt = $conn->prepare("INSERT INTO skills (skillname, skillimage, description) VALUES (?, ?, ?)");
                            $stmt->bind_param("sss", $skillName, $targetFile, $description);

                            if ($stmt->execute()) {
                                echo "Skill information added successfully!";
                            } else {
                                echo "Error: " . $stmt->error;
                            }

                            $stmt->close();
                        } else {
                            echo "Sorry, there was an error uploading your file.";
                        }
                    }
                }
                ?>
                <div class="container mt-5 addskill">
                    <div class="row">
                        <div class="col-md-6 mx-auto text-center">
                            <h2>Enter Skill Information</h2>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="skillName" class="form-label">Skill Name:</label>
                                    <input type="text" id="skillName" name="skillName" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="skillImage" class="form-label">Skill Image (JPEG, PNG):</label>
                                    <input type="file" id="skillImage" name="skillImage" class="form-control" accept="image/jpeg, image/png" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    <textarea id="description" name="description" rows="4" class="form-control" required></textarea>
                                </div>

                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php require '../include/adminFooter.php'; ?>


        </body>
    </html>
    <?php
} else {
    header("Location: ../pages/SignIn.php");
}
$conn->close();
?>