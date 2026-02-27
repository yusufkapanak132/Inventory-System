<?php
require 'auth.php';
include 'db.php';

$user_id = $_SESSION['unique_id'];

$stmt1 = $conn->prepare("SELECT status, image FROM users WHERE id = ?");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$stmt1->bind_result($status, $user_image);
$stmt1->fetch();
if ($status === "Админ") {
  header("Location: admin_dashboard.php");
  exit;
}
$stmt1->close();

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Табло</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .profile-box {
      margin: 1rem 0;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .profile-box img {
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
      <li><a href="scan.php">Сканирай продукт</a></li>
      <li><a href="items.php">Прегледай инвентара</a></li>
      <li><a href="history.php">История</a></li>
      <li><a href="earnings.php">Приходи</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h2>Добре дошъл, <?= htmlspecialchars($_SESSION['user_name'] ?? 'потребител') ?>!</h2>

  <?php if (!empty($user_image)): ?>
    <div class="profile-box">
      <img src="<?= htmlspecialchars($user_image) ?>" alt="Профилна снимка">
    </div>
  <?php endif; ?>

 

  <h3 style="margin-top:2rem;">📊 Последни доходи</h3>

  <?php if (!empty($earnings)): ?>
    <?php foreach ($earnings as $date => $dayOps): ?>
      <section style="margin-bottom: 2.5rem;">
        <h4>Дата: <?= htmlspecialchars($date) ?></h4>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Операция</th>
                <th>Тип</th>
                <th>Начало</th>
                <th>Край</th>
                <th>Време / Брой</th>
                <th>Цена</th>
                <th>Общо</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $day_total = 0;
                foreach ($dayOps as $op): 
                  $day_total += $op['earned'];
              ?>
                <tr>
                  <td><?= htmlspecialchars($op['description']) ?></td>
                  <td><?= htmlspecialchars($op['type']) ?></td>
                  <td><?= htmlspecialchars($op['start_time']) ?></td>
                  <td><?= htmlspecialchars($op['end_time']) ?></td>
                  <td><?= htmlspecialchars($op['quantity_or_time']) ?></td>
                  <td><?= number_format($op['price'], 2) ?> евро</td>
                  <td><?= number_format($op['earned'], 2) ?> евро</td>
                </tr>
              <?php endforeach; ?>
              <tr>
                <td colspan="6" style="text-align: right; font-weight:700;">Общо за деня:</td>
                <td style="font-weight:700;"><?= number_format($day_total, 2) ?> евро</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    <?php endforeach; ?>

    <p class="total">🧾 Общо всички доходи: <strong><?= number_format($grand_total, 2) ?> евро</strong></p>
  <?php else: ?>
    <p>Няма завършени операции или липсват цени за описанията.</p>
  <?php endif; ?>
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
