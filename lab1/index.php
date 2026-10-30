<?php
declare(strict_types=1);

// Крок 7: Увімкнення відображення всіх помилок на час розробки
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Крок 2: Оголошення масиву даних домену (Варіант 13: Трекер особистих витрат)
$transactions = [
    [
        'amount' => 450.50,
        'category' => 'Продукти',
        'date' => '2026-10-24',
        'description' => 'Закупівля в супермаркеті'
    ],
    [
        'amount' => 1850.00,
        'category' => 'Здоров’я та догляд',
        'date' => '2026-10-25',
        'description' => 'Замовлення доглядової косметики'
    ],
    [
        'amount' => 320.00,
        'category' => 'Транспорт',
        'date' => '2026-10-26',
        'description' => 'Квитки на потяг'
    ],
    [
        'amount' => 2400.00,
        'category' => 'Освіта та книги',
        'date' => '2026-10-27',
        'description' => 'Купівля фахової літератури та підписки'
    ],
    [
        'amount' => 195.00,
        'category' => 'Кав’ярні',
        'date' => '2026-10-28',
        'description' => 'Кава та десерт'
    ],
    [
        'amount' => 1250.75,
        'category' => 'Комунальні послуги',
        'date' => '2026-10-29',
        'description' => 'Оплата інтернету та електроенергії'
    ],
];

// Крок 3: Типізована функція форматування одного запису
function formatTransaction(array $transaction): string
{
    $formattedAmount = number_format((float)$transaction['amount'], 2, '.', ' ');
    return "{$transaction['category']} ({$transaction['description']}) — {$formattedAmount} грн від {$transaction['date']}";
}

// Крок 4: Функція з умовною логікою для визначення мітки витрати
function getExpenseLabel(float $amount): string
{
    if ($amount > 1000) {
        return 'Велика витрата';
    } else {
        return 'Звичайна витрата';
    }
}

// Крок 6: Обчислення агрегатних показників
$totalAmount = array_sum(array_column($transactions, 'amount'));
$transactionsCount = count($transactions);
$averageAmount = $transactionsCount > 0 ? $totalAmount / $transactionsCount : 0;

$largeExpenses = array_filter($transactions, fn(array $item): bool => $item['amount'] > 1000);
$largeExpensesCount = count($largeExpenses);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Практична робота №1 — Трекер особистих витрат (Варіант 13)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Трекер особистих витрат</h1>
            <p class="subtitle">Практична робота №1 · Варіант 13</p>
        </header>

        <!-- Крок 6: Виведення агрегатного показника окремим блоком -->
        <section class="summary-cards">
            <div class="card highlight">
                <span class="card-title">Загальна сума витрат</span>
                <strong class="card-value"><?= number_format($totalAmount, 2, '.', ' ') ?> грн</strong>
            </div>
            <div class="card">
                <span class="card-title">Кількість транзакцій</span>
                <strong class="card-value"><?= $transactionsCount ?></strong>
            </div>
            <div class="card">
                <span class="card-title">Середній чек</span>
                <strong class="card-value"><?= number_format($averageAmount, 2, '.', ' ') ?> грн</strong>
            </div>
            <div class="card">
                <span class="card-title">Великих витрат (більше 1000 грн)</span>
                <strong class="card-value"><?= $largeExpensesCount ?></strong>
            </div>
        </section>

        <!-- Крок 5: Виведення даних у вигляді HTML-таблиці через foreach -->
        <section class="table-section">
            <h2>Історія транзакцій</h2>
            <table>
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Дата</th>
                        <th>Категорія</th>
                        <th>Короткий опис (formatTransaction)</th>
                        <th>Сума (грн)</th>
                        <th>Статус / Мітка</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $index => $transaction): ?>
                        <?php
                            $amount = (float)$transaction['amount'];
                            $label = getExpenseLabel($amount);
                            $isLarge = $amount > 1000;
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($transaction['date']) ?></td>
                            <td><?= htmlspecialchars($transaction['category']) ?></td>
                            <td><?= htmlspecialchars(formatTransaction($transaction)) ?></td>
                            <td class="amount"><?= number_format($amount, 2, '.', ' ') ?></td>
                            <td>
                                <span class="badge <?= $isLarge ? 'badge-danger' : 'badge-normal' ?>">
                                    <?= htmlspecialchars($label) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>