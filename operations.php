<?php
require 'admin_auth.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_operation'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO operations_prices (name, type, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $type, $price);
    $stmt->execute();
    $stmt->close();
    header("Location: operations.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM operations_prices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: operations.php");
    exit;
}

$edit_operation = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM operations_prices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_operation = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_operation'])) {
    $id = $_POST['operation_id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE operations_prices SET name = ?, type = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $name, $type, $price, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: operations.php");
    exit;
}

$operations = $conn->query("SELECT * FROM operations_prices ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Операции</title>
  <link rel="stylesheet" href="admin-style.css" />
</head>
<body>

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
  <h1><a href="admin_dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="users.php">Потребители</a></li>
      <li><a href="inventory_edit.php">Инвентар</a></li>
      <li><a href="user_operations.php">Операции на потребители</a></li>
      <li><a href="operations.php" class="active">Операции</a></li>
      <li><a href="code_generator.php">Генератор на баркод</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h2><?= $edit_operation ? 'Редактирай операция' : 'Добави операция' ?></h2>

  <form method="POST" class="user-form" autocomplete="off">
    <?php if ($edit_operation): ?>
      <input type="hidden" name="operation_id" value="<?= htmlspecialchars($edit_operation['id']) ?>">
    <?php endif; ?>

    <input type="text" name="name" placeholder="Име на операция" required value="<?= $edit_operation ? htmlspecialchars($edit_operation['name']) : '' ?>" />
    <input type="text" name="type" placeholder="Тип (например: На час, На бройка)" required value="<?= $edit_operation ? htmlspecialchars($edit_operation['type']) : '' ?>" />
    <input type="number" step="0.01" name="price" placeholder="Цена" required value="<?= $edit_operation ? htmlspecialchars($edit_operation['price']) : '' ?>" />

    <button type="submit" name="<?= $edit_operation ? 'update_operation' : 'add_operation' ?>">
      <?= $edit_operation ? 'Запази промените' : 'Добави' ?>
    </button>
    <?php if ($edit_operation): ?>
      <a href="operations.php" class="btn btn-secondary">Отказ</a>
    <?php endif; ?>
  </form>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Име</th>
          <th>Тип</th>
          <th>Цена</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($operation = $operations->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($operation['id']) ?></td>
          <td><?= htmlspecialchars($operation['name']) ?></td>
          <td><?= htmlspecialchars($operation['type']) ?></td>
          <td><?= htmlspecialchars($operation['price']) ?></td>
          <td>
            <a href="operations.php?edit=<?= $operation['id'] ?>" class="btn btn-secondary" title="Редактирай">✏️</a>
            <a href="operations.php?delete=<?= $operation['id'] ?>" onclick="return confirm('Сигурни ли сте?')" class="btn btn-danger" title="Изтрий">❌</a>
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
