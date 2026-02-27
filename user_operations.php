<?php
require 'admin_auth.php';
include 'db.php';

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM operations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: user_operations.php");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$stmt = $conn->prepare("
  SELECT u.name AS user_name, o.id, i.name AS item, o.start_time, o.end_time, o.description
  FROM operations o
  JOIN users u ON o.user_id = u.id
  JOIN inventory i ON o.inventory_id = i.id
  WHERE u.name LIKE CONCAT('%', ?, '%')
  ORDER BY u.name, o.start_time DESC
");
$stmt->bind_param('s', $search);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Операции на потребители</title>
  <link rel="stylesheet" href="admin-style.css">
</head>
<body>

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
  <h1><a href="admin_dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="users.php">Потребители</a></li>
      <li><a href="inventory_edit.php">Инвентар</a></li>
      <li><a href="user_operations.php" class="active">Операции на потребители</a></li>
      <li><a href="operations.php">Операции</a></li>
      <li><a href="code_generator.php">Генератор на баркод</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h2>Операции на потребители</h2>

  <form method="GET" class="user-form" style="max-width: 400px;">
    <input type="text" name="search" placeholder="Търси по потребител..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Търсене</button>
  </form>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Потребител</th>
          <th>Обект</th>
          <th>Начало</th>
          <th>Край</th>
          <th>Описание</th>
          <th>Действия</th> 
        </tr>
      </thead>
      <tbody>
        <?php if ($res->num_rows > 0): ?>
          <?php while($r = $res->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($r['user_name']) ?></td>
              <td><?= htmlspecialchars($r['item']) ?></td>
              <td><?= htmlspecialchars($r['start_time']) ?></td>
              <td><?= htmlspecialchars($r['end_time']) ?></td>
              <td><?= htmlspecialchars($r['description']) ?></td>
              <td>
                <a href="user_operations.php?delete=<?= $r['id'] ?>" 
                   onclick="return confirm('Сигурни ли сте, че искате да изтриете тази операция?')" 
                   title="Изтрий" 
                   style="color: red; text-decoration: none; font-weight: bold;">
                   ❌
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">Няма намерени резултати.</td></tr>
        <?php endif; ?>
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
