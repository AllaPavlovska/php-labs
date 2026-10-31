<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Отримуємо поточний запис
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = :id");
$stmt->execute([':id' => $id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    die("Транзакцію не знайдено.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $date = trim($_POST['transaction_date'] ?? '');

    if ($amount === '' || !is_numeric($amount) || (float)$amount <= 0) {
        $error = 'Введіть коректну суму.';
    } else {
        // Оновлення через підготовлений запит (UPDATE)
        $updateStmt = $pdo->prepare("UPDATE transactions SET amount = :amount, category = :category, transaction_date = :date WHERE id = :id");
        $updateStmt->execute([
            ':amount' => $amount,
            ':category' => $category,
            ':date' => $date,
            ':id' => $id
        ]);

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати транзакцію</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f6f9; color: #2c3e50; padding: 32px 16px; }
        .container { max-width: 500px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        h1 { font-size: 22px; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; }
        input, select { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; }
        .error { color: #dc2626; font-size: 13px; margin-bottom: 16px; display: block; }
        button { background: #0284c7; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: 600; cursor: pointer; }
        button:hover { background: #0369a1; }
        .back { display: block; margin-top: 16px; text-align: center; color: #64748b; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Редагувати транзакцію #<?= $transaction['id'] ?></h1>
        <?php if ($error): ?><span class="error"><?= htmlspecialchars($error) ?></span><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Сума (грн):</label>
                <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars((string)$transaction['amount']) ?>" required>
            </div>
            <div class="form-group">
                <label>Категорія:</label>
                <input type="text" name="category" value="<?= htmlspecialchars($transaction['category']) ?>" required>
            </div>
            <div class="form-group">
                <label>Дата:</label>
                <input type="date" name="transaction_date" value="<?= htmlspecialchars($transaction['transaction_date']) ?>" required>
            </div>
            <button type="submit">Оновити дані</button>
        </form>
        <a href="index.php" class="back">← Назад до списку</a>
    </div>
</body>
</html>