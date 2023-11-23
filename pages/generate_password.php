<?php
require_once '../user/SignIn.php'; 

$tutorPassword = password_hash('tutor', PASSWORD_BCRYPT);
$adminPassword = password_hash('admin', PASSWORD_BCRYPT);

echo "Tutor Password: $tutorPassword<br>";
echo "Admin Password: $adminPassword<br>";
?>
