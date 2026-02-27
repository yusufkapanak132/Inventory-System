<?php
require 'admin_auth.php';
include 'db.php';

$user_id = $_SESSION['unique_id'];


$user_stmt = $conn->prepare("SELECT name, image FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();


$all_users = $conn->query("SELECT id, name, email FROM users ORDER BY name ASC");

$query = "
    SELECT 
        o.id,
        o.description,
        o.start_time,
        o.end_time,
        TIMESTAMPDIFF(SECOND, o.start_time, o.end_time) / 3600 AS hours_worked,
        p.price,
        p.type,
        DATE(o.start_time) AS op_date
    FROM operations o
    LEFT JOIN operations_prices p 
        ON LOWER(o.description) = LOWER(p.name)
    WHERE o.user_id = ? 
      AND o.end_time IS NOT NULL
    ORDER BY o.start_time DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$earnings = [];
$grand_total = 0;

while ($row = $result->fetch_assoc()) {
    $date = $row['op_date'];

    if (!isset($row['price']) || is_null($row['price'])) {
        continue;
    }

    if ($row['type'] === 'На час') {
        $row['earned'] = ($row['hours_worked'] ?? 0) * $row['price'];
        $row['quantity_or_time'] = number_format($row['hours_worked'], 2) . ' ч';
    } elseif ($row['type'] === 'На бройка') {
        $row['earned'] = 1 * $row['price'];
        $row['quantity_or_time'] = '1 бр';
    } else {
        continue;
    }

    if (!isset($earnings[$date])) {
        $earnings[$date] = [];
    }

    $earnings[$date][] = $row;
    $grand_total += $row['earned'];
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Админ Табло</title>
  <link rel="stylesheet" href="admin-style.css">
  <style>
    .user-box {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .profile-img {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ccc;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
  <h1>Табло</h1>
  <nav>
    <ul>
      <li><a href="users.php">Потребители</a></li>
      <li><a href="inventory_edit.php">Инвентар</a></li>
      <li><a href="user_operations.php">Операции на потребители</a></li>
      <li><a href="operations.php">Операции</a></li>
      <li><a href="code_generator.php">Генератор на баркод</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <div class="dashboard-header">
    <div class="user-box">
      <img src="<?= htmlspecialchars($user_data['image'] ?? 'default.png') ?>" alt="Профилна снимка" class="profile-img">
      <h2>Добре дошъл, <?= htmlspecialchars($user_data['name'] ?? 'потребител') ?>!</h2>
    </div>
  </div>

  <h3>👥 Потребители</h3>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Номер</th>
          <th>Име</th>
          <th>Имейл</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($user = $all_users->fetch_assoc()): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
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
