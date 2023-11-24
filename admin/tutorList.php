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

function getUserById($userId) {
    global $dbcon;
    try {
        $con = $dbcon->getConnection();
        $query = "SELECT * FROM user WHERE userid=?";
        $pstmt = $con->prepare($query);
        $pstmt->execute([$userId]);
        return $pstmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $exc) {
        echo $exc->getMessage();
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $userId = $_POST['userid'];
        if ($_POST['action'] == 'delete') {
            deleteUser($userId);
        } elseif ($_POST['action'] == 'update') {
            $updateFirstName = $_POST['updateFirstName'];
            $updateLastName = $_POST['updateLastName'];
            $updateEmail = $_POST['updateEmail'];
            updateUserInformation($userId, $updateFirstName, $updateLastName, $updateEmail);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
                                <!-- Button to trigger the modal -->
                                <button class="btn" data-toggle="modal" data-target="#editModalU<?php echo $user->userid; ?>">
                                    <i class="fa-solid fa-pen-to-square" style="color: #ffffff;"></i>
                                </button>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModalU <?php echo $user->userid; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Your edit form goes here -->
                                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                                    <input type="hidden" name="userid" value="<?php echo $user->userid; ?>">
                                                    <input type="hidden" name="action" value="update">

                                                    <!-- Retrieve user information for the modal -->
                                                    <?php $userDetails = getUserById($user->userid); ?>

                                                    <!-- Add input fields for each column -->
                                                    <div class="form-group">
                                                        <label for="updateFirstName">First Name</label>
                                                        <input type="text" class="form-control" id="updateFirstName" name="updateFirstName" value="<?php echo $userDetails->firstname; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="updateLastName">Last Name</label>
                                                        <input type="text" class="form-control" id="updateLastName" name="updateLastName" value="<?php echo $userDetails->lastname; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="updateEmail">Email</label>
                                                        <input type="email" class="form-control" id="updateEmail" name="updateEmail" value="<?php echo $userDetails->email; ?>">
                                                    </div>

                                                    <!-- Add more input fields as needed -->

                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Edit Modal -->
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
</body>
</html>
