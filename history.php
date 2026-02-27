<?php
include 'db.php';
require 'auth.php';

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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>История на операции</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
  <h1><a href="dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="scan.php">Сканирай продукт</a></li>
      <li><a href="items.php">Прегледай инвентара</a></li>
      <li><a href="history.php" class="active">История</a></li>
      <li><a href="earnings.php">Приходи</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h2>История на операции</h2>

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
