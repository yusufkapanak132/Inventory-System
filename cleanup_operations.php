<?php
require 'admin_auth.php';
include 'db.php';

$stmt = $conn->prepare("
    DELETE FROM operations 
    WHERE DATE(start_time) = CURDATE()
");
if ($stmt->execute()) {
    echo "Today's operations deleted successfully.";
} else {
    echo "Error deleting operations: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
