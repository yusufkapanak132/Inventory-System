<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    header("location: dashboard.php");
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($pass, $user['password'])) {
            $_SESSION['unique_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Грешна парола.";
        }
    } else {
        $error = "Няма такъв потребител.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Вход</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #e0f7fa, #e6eeff);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      background-color: white;
      padding: 3rem 3.5rem;
      border-radius: 16px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 480px;
      animation: floatIn 0.6s ease forwards;
      transform: translateY(20px);
      opacity: 0;
    }

    @keyframes floatIn {
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      color: #1a202c;
      font-size: 1.8rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1.4rem;
    }

    label {
      font-weight: 600;
      color: #4a5568;
      margin-bottom: 0.4rem;
    }

    input[type="email"],
    input[type="password"] {
      padding: 0.75rem 1rem;
      border: 1.8px solid #cbd5e0;
      border-radius: 10px;
      font-size: 1rem;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      border-color: #38a169;
      box-shadow: 0 0 8px rgba(56, 161, 105, 0.3);
      outline: none;
    }

    button {
      padding: 0.8rem 1.2rem;
      background: linear-gradient(to right, #38a169, #2f855a);
      color: #fff;
      font-weight: 700;
      border: none;
      border-radius: 10px;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    button:hover {
      background: linear-gradient(to right, #2f855a, #276749);
      transform: scale(1.02);
    }

    p.error {
      margin-top: 1rem;
      background-color: #fde8e8;
      color: #c53030;
      padding: 0.75rem 1rem;
      border-radius: 10px;
      font-weight: 600;
      text-align: center;
    }

    @media (max-width: 520px) {
      .login-container {
        padding: 2.5rem 2rem;
        margin: 1rem;
      }
    }
  </style>
</head>
<body>

  <div class="login-container" role="main" aria-label="Login form">
    <h2>Вход в системата</h2>
    <form method="post" novalidate>
      <label for="email">Имейл:</label>
      <input type="email" id="email" name="email" placeholder="your@example.com" required autofocus />

      <label for="password">Парола:</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required />

      <button type="submit">Вход</button>
    </form>

    <?php if (isset($error)) : ?>
      <p class="error" role="alert"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
  </div>

</body>
</html>
