<?php 
require 'auth.php';
include 'db.php';

$user_id = $_SESSION['unique_id'];

$stmt = $conn->prepare("
    SELECT o.*, i.name AS inv_name, u.name AS user_name
    FROM operations o
    JOIN inventory i ON o.inventory_id = i.id
    JOIN users u ON o.user_id = u.id
    WHERE o.user_id = ?
    ORDER BY o.start_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>Сканиране</title>
  <link rel="stylesheet" href="style.css" />
  <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="scan-page">

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
 <h1><a href="dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="scan.php" class="active">Сканирай продукт</a></li>
      <li><a href="items.php">Прегледай инвентара</a></li>
      <li><a href="history.php">История</a></li>
      <li><a href="earnings.php">Приходи</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h3>Сканирай QR/Баркод</h3>
  
  <div id="reader"></div>
  
  <button id="start-scan-btn" style="display:none; margin: 1rem auto; padding: 12px 24px; font-size:1rem; cursor:pointer; border-radius:8px; background:#1abc9c; color:#fff; border:none;">
    Стартирай камерата
  </button>
  
  <p id="error-msg" style="color:#e74c3c; text-align:center; margin-top:1rem;"></p>

  <script>
    const html5QrCode = new Html5Qrcode("reader");
    const startScanBtn = document.getElementById("start-scan-btn");
    const errorMsg = document.getElementById("error-msg");

    function startScanner() {
      html5QrCode.start(
        { facingMode: "environment" },
        {
          fps: 10,
          qrbox: { width: 250, height: 250 },
          disableFlip: false
        },
        decodedText => {
          html5QrCode.stop().then(() => {
            window.location.href = "inventory.php?code=" + encodeURIComponent(decodedText);
          }).catch(() => {
            window.location.href = "inventory.php?code=" + encodeURIComponent(decodedText);
          });
        }
      ).catch(err => {
        console.warn("Error starting the camera:", err);
        errorMsg.textContent = "Не може да се стартира камерата. Моля, разрешете достъп до камерата и опитайте отново.";
        startScanBtn.style.display = "inline-block";
      });
    }

    
    if (navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function') {
      startScanner();
    } else {
      errorMsg.textContent = "Вашият браузър не поддържа камерата.";
    }

   
    startScanBtn.addEventListener("click", () => {
      errorMsg.textContent = "";
      startScanBtn.style.display = "none";
      startScanner();
    });
  </script>

  <h3>📋 Последни операции</h3>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Обект</th>
          <th>Потребител</th>
          <th>Начало</th>
          <th>Край</th>
          <th>Описание</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['inv_name']) ?></td>
          <td><?= htmlspecialchars($row['user_name']) ?></td>
          <td><?= htmlspecialchars($row['start_time']) ?></td>
          <td><?= htmlspecialchars($row['end_time']) ?></td>
          <td><?= htmlspecialchars($row['description']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
  const menuToggle = document.querySelector('.menu-toggle');
  const sidebar = document.getElementById('sidebar');
  menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
</script>

</body>
</html>
