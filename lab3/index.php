<?php
declare(strict_types=1);

// Підключення класів та бібліотеки функцій через require_once
require_once __DIR__ . '/classes/Transaction.php';
require_once __DIR__ . '/classes/RecurringExpense.php';
require_once __DIR__ . '/classes/Wallet.php';
require_once __DIR__ . '/lib/functions.php';

// Створення гаманця (менеджера)
$myWallet = new Wallet();

// Додавання об'єктів базового класу Transaction
$myWallet->addTransaction(new Transaction(450.50, 'Продукти', '2026-10-25'));
$myWallet->addTransaction(new Transaction(150.00, 'Кав’ярні', '2026-10-28'));

// Додавання об'єктів похідного класу RecurringExpense
$myWallet->addTransaction(new RecurringExpense(350.00, 'Комунальні послуги', '2026-10-01', 'Щомісяця'));
$myWallet->addTransaction(new RecurringExpense(1200.00, 'Здоров’я та догляд', '2026-10-15', 'Щороку'));
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Практична робота №3 — Трекер особистих витрат (ООП)</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f6f9; color: #2c3e50; padding: 32px 16px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        h1 { font-size: 24px; margin-bottom: 8px; color: #1a252f; }
        .subtitle { color: #6c757d; font-size: 14px; margin-bottom: 24px; }
        .stats { display: flex; gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; flex: 1; }
        .stat-card h3 { font-size: 13px; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .stat-card p { font-size: 20px; font-weight: 700; color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
        .badge { display: inline-block; padding: 4px 8px; font-size: 12px; font-weight: 600; border-radius: 4px; background: #e0f2fe; color: #0369a1; }
        .badge-recurring { background: #f3e8ff; color: #7e22ce; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Облік транзакцій (ООП підхід)</h1>
        <p class="subtitle">Практична робота №3 · Варіант 13 (Трекер особистих витрат)</p>

        <div class="stats">
            <div class="stat-card">
                <h3>Загальна сума витрат</h3>
                <p><?= formatMoney($myWallet->balance()) ?></p>
            </div>
            <div class="stat-card">
                <h3>Витрати на «Продукти»</h3>
                <p><?= formatMoney($myWallet->totalByCategory('Продукти')) ?></p>
            </div>
        </div>

        <h2>Список усіх транзакцій</h2>
        <table>
            <thead>
                <tr>
                    <th>Тип</th>
                    <th>Категорія</th>
                    <th>Сума</th>
                    <th>Дата</th>
                    <th>Деталі / Регулярність</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myWallet->getTransactions() as $tx): ?>
                    <tr>
                        <td>
                            <?php if ($tx instanceof RecurringExpense): ?>
                                <span class="badge badge-recurring">Регулярна</span>
                            <?php else: ?>
                                <span class="badge">Звичайна</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($tx->getCategory()) ?></td>
                        <td><strong><?= formatMoney($tx->getAmount()) ?></strong></td>
                        <td><?= htmlspecialchars(monthName($tx->getDate())) ?></td>
                        <td>
                            <small><?= htmlspecialchars($tx->getInfo()) ?></small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>