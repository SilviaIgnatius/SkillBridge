<?php
require_once '../classes/DbConnector.php';

use classes\DbConnector;

$dbcon = new DbConnector();

function deleteUser($userId) {
    global $dbcon;
    try {
        $con = $dbcon->getConnection();
        $query = "DELETE FROM user WHERE userid=?";
        $pstmt = $con->prepare($query);
        $pstmt->execute([$userId]);
        return true;
    } catch (PDOException $exc) {
        echo $exc->getMessage();
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $userId = $_POST['userid'];
        if ($_POST['action'] == 'delete') {
            // Delete user
            deleteUser($userId);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/admin.css">
    </head>
    <body>
        <?php include '../include/adminNav.php'; ?>
        <div class="content">
            <button class="btn btn-danger logout-button">Logout</button>
            <table class="table table-hover table-dark">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Username</th>
                        <th scope="col" colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $con = $dbcon->getConnection();
                        $query = "SELECT userid, firstname, lastname, email, username FROM user WHERE role = 'tutor'";
                        $pstmt = $con->prepare($query);
                        $pstmt->execute();
                        $rs = $pstmt->fetchAll(PDO::FETCH_OBJ);
                        $i = 1;
                        foreach ($rs as $user) {
                            ?>

                            <tr>
                                <th scope="row"><?php echo $i; ?></th>
                                <td><?php echo $user->firstname; ?></td>
                                <td><?php echo $user->lastname; ?></td>
                                <td><?php echo $user->email; ?></td>
                                <td><?php echo $user->username; ?></td>
                                <td>
                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                        <input type="hidden" name="userid" value="<?php echo $user->userid; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn">
                                            <span><i class="fas fa-trash" style="color: #ffffff;"></i></span>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <button class="btn">
                                        <i class="fa-solid fa-pen-to-square" style="color: #ffffff;"></i>
                                    </button>
                                </td>
                            </tr>

                            <?php
                            $i++;
                        }
                    } catch (PDOException $exc) {
                        echo $exc->getMessage();
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php include '../include/adminFooter.php'; ?>

        <!-- Bootstrap JS CDN -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-GLhlTQ8iKGu+2F6PKckGEKf538i4Zv3qMP7IcNTeDsdH+6Qjp0F4L8RniCzoI+"
        crossorigin="anonymous"></script>
    </body>
</html>
