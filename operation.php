<?php
include 'db.php';
require 'auth.php';

$user_id = $_SESSION['unique_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventory_id = $_POST['inventory_id'] ?? null;
    $description = $_POST['description'] ?? null;

    if (!$inventory_id || !$description) {
        echo "Липсва информация.";
        exit;
    }

    if (isset($_POST['start'])) {
      
        $stmt = $conn->prepare("SELECT * FROM operations WHERE user_id = ? AND end_time IS NULL ORDER BY start_time DESC LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $openOperation = $result->fetch_assoc();

        if ($openOperation) {
            if ((int)$openOperation['inventory_id'] === (int)$inventory_id) {
                
                $stmt = $conn->prepare("UPDATE operations SET end_time = NOW() WHERE id = ?");
                $stmt->bind_param("i", $openOperation['id']);
                $stmt->execute();

                header("Location: dashboard.php");
                exit;
            } else {
               
                $stmt = $conn->prepare("UPDATE operations SET end_time = NOW() WHERE id = ?");
                $stmt->bind_param("i", $openOperation['id']);
                $stmt->execute();
            }
        }

       
        $stmt = $conn->prepare("INSERT INTO operations (user_id, inventory_id, start_time, description) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("iis", $user_id, $inventory_id, $description);
        $stmt->execute();

        header("Location: scan.php");
        exit;
    }

    if (isset($_POST['end'])) {
        
        header("Location: dashboard.php");
        exit;
    }
}
?>
