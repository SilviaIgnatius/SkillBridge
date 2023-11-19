<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Modules</title>
    </head>
    <body>
        <?php
        if (isset($_GET['userid']) && isset($_GET['skillname']) && isset($_GET['username'])) {
            $userid = $_GET['userid'];
            $skillname = $_GET['skillname'];
            $name = $_GET['username'];

            // Database connection code here (replace with your own credentials)
            $servername = "localhost";
            $db_username = "root";
            $db_password = "";
            $dbname = "skillbridge";

            $conn = new mysqli($servername, $db_username, $db_password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT m.moduleid, m.modulename, m.description
                FROM module m
                JOIN skill_module sm ON m.moduleid = sm.moduleid
                JOIN skills s ON sm.skillid = s.skillid
                JOIN user u ON sm.userid = u.userid
                WHERE sm.userid = ? AND s.skillname = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $userid, $skillname);
            $stmt->execute();
            $result = $stmt->get_result();

            echo '<h2>Skill: ' . $skillname . '</h2>';
            echo '<h3>Tutor: ' . $name . '</h3>';

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="module-card">
                    <h4>' . $row["modulename"] . '</h4>
                    <p>' . $row["description"] . '</p>
                    </div>';

                    // Fetch and display module resources for this module
                    $moduleid = $row["moduleid"];

                    // Fetch PDF resources for this module
                    $pdf_sql = "SELECT pdfname, file
                            FROM pdf_resource
                            WHERE moduleid = ?";
                    $pdf_stmt = $conn->prepare($pdf_sql);
                    $pdf_stmt->bind_param("i", $moduleid);
                    $pdf_stmt->execute();
                    $pdf_result = $pdf_stmt->get_result();

                    // Fetch video resources for this module
                    $video_sql = "SELECT videoname, videourl
                              FROM video_resource
                              WHERE moduleid = ?";
                    $video_stmt = $conn->prepare($video_sql);
                    $video_stmt->bind_param("i", $moduleid);
                    $video_stmt->execute();
                    $video_result = $video_stmt->get_result();

                    if ($pdf_result->num_rows > 0 || $video_result->num_rows > 0) {
                        echo '<h4>Resources:</h4>';
                        echo '<ul>';
                        while ($pdf_row = $pdf_result->fetch_assoc()) {
                            $pdfUrl = '/Skills/' . $pdf_row["file"]; // Construct the URL to the PDF
                            echo '<li><a href="' . $pdfUrl . '" target="_blank">' . $pdf_row["pdfname"] . '</a></li>';
                        }

                        while ($video_row = $video_result->fetch_assoc()) {
                            echo '<li><a href="' . $video_row["videourl"] . '" target="_blank"' . '">' . $video_row["videoname"] . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    $pdf_stmt->close();
                    $video_stmt->close();
                }
            } else {
                echo "No modules found for this tutor";
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "User ID, Skill Name, or Username not provided.";
        }
        ?>
    </body>
</html>
