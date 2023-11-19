<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Skills</title>
    </head>
    <body>
        <?php
        // Database connection code (replace with your own credentials)
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "skillbridge";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL query to fetch skills from the database
        $sql = "SELECT * FROM skills";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<a href="sample_skill_tutor.php?skillid=' . $row["skillid"] . '&skillname=' . urlencode($row["skillname"]) . '">
            <div class="skill-card">
            
                <h4>' . $row["skillname"] . '</h4> </a>    
                    <img class="card-image" src="$row["skillimage"] ">' . '</>
                    <p class="card-text"> * ' . $row["description"] . '</p>
            </div>
            ';
            }
        } else {
            echo "No skills found";
        }

        $conn->close();
        ?>
    </body>
</html>
