<?php
require 'admin_auth.php';

require __DIR__ . '/vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$generatedFile = null;
$error = null;

$barcodesDir = __DIR__ . '/barcodes';

if (!is_dir($barcodesDir)) {
    mkdir($barcodesDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    if (!empty($code)) {
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($code, \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128);

            if (empty($barcodeData)) {
                $error = "Неуспешно генериране на баркод.";
            } else {
                
                $fileName = preg_replace('/[^a-zA-Z0-9\-]/', '_', $code) . '.png';
                $filePath = $barcodesDir . '/' . $fileName;

                file_put_contents($filePath, $barcodeData);
                $generatedFile = 'barcodes/' . $fileName;
            }
        } catch (Exception $e) {
            $error = "Грешка при генериране: " . $e->getMessage();
        }
    } else {
        $error = "Моля, въведете валиден код.";
    }
}

if (isset($_GET['delete'])) {
    $deleteFile = basename($_GET['delete']);
    $deletePath = realpath($barcodesDir . '/' . $deleteFile);

    if ($deletePath && strpos($deletePath, realpath($barcodesDir)) === 0 && file_exists($deletePath)) {
        unlink($deletePath);
        header('Location: code_generator.php');
        exit;
    } else {
        $error = "Невалиден файл за изтриване.";
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Генератор на баркод</title>
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .barcode-preview {
            margin-top: 2rem;
            text-align: center;
        }
        .barcode-preview img {
            max-width: 100%;
            height: auto;
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .barcode-preview p {
            font-size: 1.2rem;
            margin-top: 0.5rem;
        }
        .barcode-preview .buttons {
            margin-top: 1rem;
        }
        .barcode-preview .buttons a {
            margin-right: 10px;
        }
        .main-content form {
            max-width: 400px;
        }
    </style>
</head>
<body>

<button class="menu-toggle">&#9776;</button>

<aside class="sidebar" id="sidebar">
  <h1><a href="admin_dashboard.php">Табло</a></h1>
  <nav>
    <ul>
      <li><a href="users.php">Потребители</a></li>
      <li><a href="inventory_edit.php">Инвентар</a></li>
      <li><a href="user_operations.php">Операции на потребители</a></li>
      <li><a href="operations.php">Операции</a></li>
      <li><a href="code_generator.php" class="active">Генератор на баркод</a></li>
      <li><a href="logout.php">Изход</a></li>
    </ul>
  </nav>
</aside>

<main class="main-content">
    <h2>Генерирай баркод</h2>

    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="user-form">
        <label for="code">Въведи код (напр. A-0014-Z):</label>
        <input type="text" name="code" id="code" required value="<?= isset($_POST['code']) ? htmlspecialchars($_POST['code']) : '' ?>">
        <button type="submit">Генерирай</button>
    </form>

    <?php if ($generatedFile): ?>
        <div class="barcode-preview">
            <img src="<?= htmlspecialchars($generatedFile) . '?t=' . time() ?>" alt="Баркод">
            <p><?= htmlspecialchars($_POST['code']) ?></p>
            <div class="buttons">
                <a class="btn btn-secondary" href="<?= htmlspecialchars($generatedFile) ?>" download>Изтегли</a>
                <a class="btn btn-danger" href="?delete=<?= urlencode(basename($generatedFile)) ?>" onclick="return confirm('Сигурни ли сте, че искате да изтриете този баркод?');">Изтрий</a>
            </div>
        </div>
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
