<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Module Resources</title>
</head>
<body>
    <?php
    // Check if the module ID, skill name, and username are provided in the URL
    if (isset($_GET['moduleid']) && isset($_GET['skillname']) && isset($_GET['username'])) {
        $moduleid = $_GET['moduleid'];
        $skillname = $_GET['skillname'];
        $username = $_GET['username'];

        // Database connection code (replace with your own credentials)
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "skillbridge";

        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Use prepared statements to prevent SQL injection
        $sql = "SELECT r.resourcename, r.file, r.url
                FROM module_resources r
                WHERE r.moduleid = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $moduleid); // 'i' indicates an integer parameter
        $stmt->execute();
        $result = $stmt->get_result(); // Get the result set

        echo '<h2>Skill: ' . $skillname . '</h2>';
        echo '<h3>Tutor: ' . $username . '</h3>';
        echo '<h4>Module ID: ' . $moduleid . '</h4>';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="resource-card">';
                echo '<h4>Resource Name: ' . $row["resoursename"] . '</h4>';
                if (!empty($row["file"])) {
                    echo '<p><a href="path_to_your_resources_directory/' . $row["file"] . '" target="_blank">View PDF</a></p>';
                }
                if (!empty($row["url"])) {
                    echo '<p><a href="' . $row["url"] . '" target="_blank">View Video</a></p>';
                }
                echo '</div>';
            }
        } else {
            echo "No resources found for this module";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Module ID, Skill Name, or Username not provided.";
    }
    ?>
</body>
</html>
