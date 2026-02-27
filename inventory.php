<?php
require 'auth.php';
include 'db.php';

$code = $_GET['code'] ?? '';
$item = null;

if ($code) {
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    if (!$item) {
        $error = "Няма такъв обект.";
    }
}
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
  <h1>Табло</h1>
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
  <h2>Данни за обект</h2>

  <?php if (!empty($error)): ?>
    <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
  <?php elseif ($item): ?>
    <h3><?= htmlspecialchars($item['name']) ?></h3>
    <p><strong>Локация:</strong> <?= htmlspecialchars($item['location']) ?></p>
    <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($item['description'])) ?></p>

    <form method="post" action="operation.php">
      <input type="hidden" name="inventory_id" value="<?= $item['id'] ?>">
      <label for="description">Описание на операцията:</label><br>
      <select id="description" name="description" required>
        <option value="Гладене">Гладене</option>
        <option value="Почистване">Почистване</option>
        <option value="Шиене">Шиене</option>
        <option value="Обличане">Обличане</option>
      </select>
      <br><br>
      <button type="submit" name="start">Начало</button>
      <button type="submit" name="end">Откажи</button>
    </form>
  <?php else: ?>
    <p>Моля, сканирайте или въведете код, за да видите детайли за обекта.</p>
  <?php endif; ?>

  <p><a href="items.php">⬅ Назад към инвентара</a></p>
</main>

<script>
  const menuToggle = document.querySelector('.menu-toggle');
  const sidebar = document.getElementById('sidebar');
  menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
</script>

</body>
</html>
