<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Top 20 Tutors</title>
</head>
<body>
    <?php
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "skillbridge";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT u.userid, u.username,  w.no_of_tokens
            FROM user u
            INNER JOIN wallet w ON u.userid = w.userid
            WHERE u.role = 'tutor'
            ORDER BY w.no_of_tokens DESC
            LIMIT 20";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table>';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>User Name</th>';
        echo '<th>Token Count</th>';
        echo '</tr>';
        $count = 1;

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row["username"] . '</td>';
            echo '<td>' . $row["no_of_tokens"] . '</td>';
            echo '</tr>';
            $count++;
        }

        echo '</table>';
    } else {
        echo "No leading tutors found.";
    }

    $conn->close();
    ?>
</body>
</html>
