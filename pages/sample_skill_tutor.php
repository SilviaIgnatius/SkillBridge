<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Skill Tutors</title>
</head>
<body>
    <?php
    // Check if the skill ID and skill name are provided in the URL
    if (isset($_GET['skillid']) && isset($_GET['skillname'])) {
        $skill_id = $_GET['skillid'];
        $skillname = $_GET['skillname'];

        // Database connection code (replace with your own credentials)
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "skillbridge";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Use prepared statements to prevent SQL injection
        $sql = "SELECT ts.userid, u.username, ts.description
                FROM tutor_skill ts
                JOIN user u ON ts.userid = u.userid
                WHERE ts.skillid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $skill_id); // 'i' indicates an integer parameter
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<h2>' . $skillname . '</h2>';
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="tutor-card">
                    <a href="sample_tutor_skill_module.php?userid=' . $row["userid"] . '&skillname=' . urlencode($skillname) . '&username=' . urlencode($row["username"]) . '">
                        <h4>' . $row["username"] . '</h4>
                    </a>
                    <p> >> ' . $row["description"] . '</p>
                </div>';
            }
        } else {
            echo "No tutors found for this skill";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Skill ID or Skill Name not provided.";
    }
    ?>
</body>
</html>
