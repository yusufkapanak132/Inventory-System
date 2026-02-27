<?php
require 'admin_auth.php';
include 'db.php';

if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $status = $_POST['status'];
    $created_at = date('Y-m-d H:i:s');
    $unique_id = rand(time(), 100000000);

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = 'uploads/' . uniqid('user_', true) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, image, status, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $unique_id, $name, $email, $password, $image, $status, $created_at);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit;
}

// Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit;
}


$edit_user = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $image = $_POST['existing_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = 'uploads/' . uniqid('user_', true) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, image=?, status=? WHERE id=?");
    $stmt->bind_param("sssss", $name, $email, $image, $status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit;
}


$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Потребители</title>
  <link rel="stylesheet" href="admin-style.css" />
</head>
<body>

<button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>

<aside class="sidebar" id="sidebar">
  <h1><a href="admin_dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="users.php" class="active">Потребители</a></li>
      <li><a href="inventory_edit.php">Инвентар</a></li>
      <li><a href="user_operations.php">Операции на потребители</a></li>
      <li><a href="operations.php">Операции</a></li>
      <li><a href="code_generator.php">Генератор на баркод</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
  <h2><?= $edit_user ? 'Редактирай потребител' : 'Добави потребител' ?></h2>

  <form method="POST" enctype="multipart/form-data" class="user-form">
    <?php if ($edit_user): ?>
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($edit_user['id']) ?>" />
      <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_user['image']) ?>" />
    <?php endif; ?>

    <input type="text" name="name" placeholder="Име" value="<?= $edit_user ? htmlspecialchars($edit_user['name']) : '' ?>" required />
    <input type="email" name="email" placeholder="Имейл" value="<?= $edit_user ? htmlspecialchars($edit_user['email']) : '' ?>" required />
    <?php if (!$edit_user): ?>
      <input type="password" name="password" placeholder="Парола" required />
    <?php endif; ?>
    <input type="file" name="image" accept="image/*" />
    <select name="status" required>
      <option value="Админ" <?= $edit_user && $edit_user['status'] === 'Админ' ? 'selected' : '' ?>>Админ</option>
      <option value="Работник" <?= $edit_user && $edit_user['status'] === 'Работник' ? 'selected' : '' ?>>Работник</option>
    </select>
    <button type="submit" name="<?= $edit_user ? 'update_user' : 'add_user' ?>">
      <?= $edit_user ? 'Запази промените' : 'Добави' ?>
    </button>
    <?php if ($edit_user): ?>
      <a href="users.php" class="btn btn-secondary">Отказ</a>
    <?php endif; ?>
  </form>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Снимка</th>
          <th>Име</th>
          <th>Имейл</th>
          <th>Статус</th>
          <th>Създаден на</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
        <tr>
          <td>
            <?php if (!empty($user['image'])): ?>
              <img src="<?= htmlspecialchars($user['image']) ?>" width="40" alt="user image" />
            <?php else: ?>
              <span>—</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($user['name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['status']) ?></td>
          <td><?= htmlspecialchars($user['created_at']) ?></td>
          <td>
            <a href="users.php?edit=<?= $user['id'] ?>" class="btn btn-secondary" title="Редактирай">✏️</a>
            <a href="users.php?delete=<?= $user['id'] ?>" onclick="return confirm('Сигурни ли сте?')" class="btn btn-danger" title="Изтрий">❌</a>
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
