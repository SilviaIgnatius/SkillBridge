<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skillName = $_POST['skillName'];
    $moduleName = $_POST['moduleName'];
    $videoName = $_POST['videoName'];
    $pdfName = $_POST['pdfName'];
    $videoURL = $_POST['url'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "skillbridge";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT s.skillid, m.moduleid
            FROM skills s
            JOIN skill_module sm ON s.skillid = sm.skillid
            JOIN module m ON sm.moduleid = m.moduleid
            WHERE s.skillname = '$skillName' AND m.modulename = '$moduleName'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $skillId = $row['skillid'];
        $moduleId = $row['moduleid'];

        if (isset($_FILES['pdfFile'])) {
            $uploadDir = 'upload/';
            $pdfFile = $_FILES['pdfFile'];
            $pdfFileName = $pdfFile['name'];
            $pdfFilePath = $uploadDir . $pdfFileName;

            if (move_uploaded_file($pdfFile['tmp_name'], $pdfFilePath)) {

                $sql = "INSERT INTO pdf_resource (moduleid, resourceid, pdfname, file)
                        VALUES ('$moduleId', NULL, '$pdfName', '$pdfFilePath')";
                if ($conn->query($sql) === TRUE) {
                    $pdfResourceID = $conn->insert_id;
                } else {
                    echo "Error inserting PDF details into the database: " . $conn->error;
                }
            } else {
                echo "Error moving PDF file.";
            }
        }

        if (!empty($videoURL)) {
            $sql = "INSERT INTO video_resource (moduleid, resourceid, videoname, videourl)
                    VALUES ('$moduleId', NULL, '$videoName', '$videoURL')";
            if ($conn->query($sql) === TRUE) {
                $videoResourceID = $conn->insert_id;
            } else {
                echo "Error inserting video link details into the database: " . $conn->error;
            }
        }

        echo "Resource(s) uploaded successfully!";
    } else {
        echo "Skill Name or Module Name does not exist. Please check your input.";
    }

    $conn->close();
}
?>
