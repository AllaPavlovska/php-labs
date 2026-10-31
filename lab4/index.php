<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

// Видалення запису (DELETE)
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);
    header('Location: index.php');
    exit;
}

// Отримання всіх транзакцій (SELECT)
$stmt = $pdo->query("SELECT * FROM transactions ORDER BY transaction_date DESC, id DESC");
$transactions = $stmt->fetchAll();

// Розрахунок загального балансу
$balanceStmt = $pdo->query("SELECT SUM(amount) AS balance FROM transactions");
$balanceRow = $balanceStmt->fetch();
$totalBalance = $balanceRow['balance'] ?? 0.00;
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Трекер витрат — Практична №4 (PDO)</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f6f9; color: #2c3e50; padding: 32px 16px; line-height: 1.6; }
        .container { max-width: 850px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        h1 { font-size: 24px; margin-bottom: 8px; color: #1a252f; }
        .subtitle { color: #6c757d; font-size: 14px; margin-bottom: 24px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .btn { background: #2563eb; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .btn-danger { background: #dc2626; padding: 6px 10px; font-size: 12px; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-edit { background: #0284c7; padding: 6px 10px; font-size: 12px; margin-right: 6px; }
        .btn-edit:hover { background: #0369a1; }
        .balance-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px 20px; border-radius: 8px; margin-bottom: 24px; }
        .balance-card h3 { font-size: 13px; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .balance-card p { font-size: 22px; font-weight: 700; color: #2563eb; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Трекер особистих витрат (База даних PDO)</h1>
        <p class="subtitle">Практична робота №4 · Варіант 13</p>

        <div class="top-bar">
            <div class="balance-card" style="margin-bottom: 0; flex: 1; margin-right: 16px;">
                <h3>Загальна сума витрат</h3>
                <p><?= number_format((float)$totalBalance, 2, ',', ' ') ?> грн</p>
            </div>
            <a href="add.php" class="btn">+ Додати транзакцію</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Сума</th>
                    <th>Категорія</th>
                    <th>Дата</th>
                    <th style="text-align: right;">Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr><td colspan="5" style="text-align: center; color: #64748b;">Транзакцій поки немає.</td></tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><?= $tx['id'] ?></td>
                            <td><strong><?= number_format((float)$tx['amount'], 2, ',', ' ') ?> грн</strong></td>
                            <td><?= htmlspecialchars($tx['category']) ?></td>
                            <td><?= htmlspecialchars($tx['transaction_date']) ?></td>
                            <td style="text-align: right;">
                                <a href="edit.php?id=<?= $tx['id'] ?>" class="btn btn-edit">Редагувати</a>
                                <a href="index.php?delete_id=<?= $tx['id'] ?>" class="btn btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити цей запис?');">Видалити</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>