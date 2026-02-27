<?php
require 'auth.php';
include 'db.php';

$stmt = $conn->prepare("SELECT * FROM inventory ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Инвентар</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
<h1><a href="dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="scan.php">Сканирай продукт</a></li>
      <li><a href="items.php" class="active">Прегледай инвентара</a></li>
      <li><a href="history.php">История</a></li>
      <li><a href="earnings.php">Приходи</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h2>Инвентар</h2>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Име</th>
          <th>Локация</th>
          <th>Описание</th>
          <th>Код</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($item = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= htmlspecialchars($item['location']) ?></td>
          <td><?= htmlspecialchars($item['description']) ?></td>
          <td><?= htmlspecialchars($item['code']) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
  const menuToggle = document.querySelector('.menu-toggle');
  const sidebar = document.getElementById('sidebar');

  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });
</script>

</body>
</html>
