<?php
require 'admin_auth.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $code = $_POST['code'];

    if (isset($_POST['item_id'])) {
        $stmt = $conn->prepare("UPDATE inventory SET name=?, location=?, description=?, status=?, code=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $location, $description, $status, $code, $_POST['item_id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO inventory (name, location, description, status, code) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $location, $description, $status, $code);
    }
    $stmt->execute();
    header("Location: inventory_edit.php");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: inventory_edit.php");
    exit;
}

$edit = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$item = $edit ? $conn->query("SELECT * FROM inventory WHERE id = $edit")->fetch_assoc() : null;

$all = $conn->query("SELECT * FROM inventory ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Инвентар</title>
  <link rel="stylesheet" href="admin-style.css" />
</head>
<body>

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
  <h1><a href="admin_dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="users.php">Потребители</a></li>
      <li><a href="inventory_edit.php" class="active">Инвентар</a></li>
      <li><a href="user_operations.php">Операции на потребители</a></li>
      <li><a href="operations.php">Операции</a></li>
      <li><a href="code_generator.php">Генератор на баркод</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h2><?= $item ? 'Редактирай предмет' : 'Добави нов предмет' ?></h2>

  <form method="POST" class="user-form" autocomplete="off">
    <?php if ($item): ?>
      <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
    <?php endif; ?>

    <input name="name" placeholder="Име" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required>
    <input name="code" placeholder="Код (напр. A-0012-Z)" value="<?= htmlspecialchars($item['code'] ?? '') ?>" required>
    <input name="location" placeholder="Локация" value="<?= htmlspecialchars($item['location'] ?? '') ?>" required>
    <input name="description" placeholder="Описание" value="<?= htmlspecialchars($item['description'] ?? '') ?>">
    <input name="status" placeholder="Статус" value="<?= htmlspecialchars($item['status'] ?? '') ?>" required>

    <button type="submit"><?= $item ? 'Запази' : 'Добави' ?></button>
    <?php if ($item): ?>
      <a href="inventory_edit.php" class="btn btn-secondary">Отказ</a>
    <?php endif; ?>
  </form>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Име</th>
          <th>Код</th>
          <th>Локация</th>
          <th>Описание</th>
          <th>Статус</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($i = $all->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($i['name']) ?></td>
          <td><?= htmlspecialchars($i['code']) ?></td>
          <td><?= htmlspecialchars($i['location']) ?></td>
          <td><?= htmlspecialchars($i['description']) ?></td>
          <td><?= htmlspecialchars($i['status']) ?></td>
          <td>
            <a href="inventory_edit.php?edit=<?= $i['id']?>" class="btn btn-secondary" title="Редактирай">✏️</a>
            <a href="inventory_edit.php?delete=<?= $i['id']?>" onclick="return confirm('Сигурни ли сте?')" class="btn btn-danger" title="Изтрий">❌</a>
          </td>
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
