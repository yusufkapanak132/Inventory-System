<?php
session_start();

if (!isset($_SESSION['unique_id'])) {
   
    header("Location: index.php");
    exit;
}

include 'db.php';

$user_id = $_SESSION['unique_id'];

$stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if ($user['status'] !== 'Админ') {
        
        header("Location: dashboard.php");
        exit;
    }
} else {
  
    header("Location: index.php");
    exit;
}

$stmt->close();
?>
