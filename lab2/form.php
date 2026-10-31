<?php
declare(strict_types=1);

// Увімкнення відображення помилок на час розробки
error_reporting(E_ALL);
ini_set('display_errors', '1');

$errors = [];
$successMessage = '';

// Початкові значення для полів форми (щоб не зникали при помилках)
$amount = '';
$category = '';
$date = date('Y-m-d'); // Поточна дата за замовчуванням

// Доступні категорії для випадаючого списку (Варіант 13)
$allowedCategories = [
    'Продукти',
    'Здоров’я та догляд',
    'Транспорт',
    'Освіта та книги',
    'Кав’ярні',
    'Комунальні послуги',
    'Інше'
];

// Крок 3: Перевірка чи форму надіслано методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $date = trim($_POST['date'] ?? '');

    // 1. Валідація суми (amount - число більше 0)
    if ($amount === '') {
        $errors['amount'] = 'Сума транзакції є обов’язковою.';
    } elseif (!is_numeric($amount) || (float)$amount <= 0) {
        $errors['amount'] = 'Сума має бути числом більшим за 0.';
    }

    // 2. Валідація категорії (category обов'язкова і має бути в списку)
    if ($category === '') {
        $errors['category'] = 'Будь ласка, оберіть категорію витрат.';
    } elseif (!in_array($category, $allowedCategories, true)) {
        $errors['category'] = 'Обрано неприпустиму категорію.';
    }

    // 3. Валідація дати (date - коректна дата)
    if ($date === '') {
        $errors['date'] = 'Дата транзакції є обов’язковою.';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            $errors['date'] = 'Введено некоректний формат дати.';
        }
    }

    // Якщо немає помилок - обробляємо дані успішно
    if (empty($errors)) {
        $successMessage = 'Транзакцію успішно додано!';
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Практична робота №2 — Трекер особистих витрат (Варіант 13)</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f6f9; color: #2c3e50; line-height: 1.6; padding: 32px 16px; }
        .container { max-width: 650px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06); }
        h1 { font-size: 24px; margin-bottom: 8px; color: #1a252f; }
        .subtitle { color: #6c757d; font-size: 14px; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #334155; }
        input[type="number"], input[type="date"], select { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; transition: border-color 0.2s; }
        input:focus, select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .error-msg { color: #dc2626; font-size: 13px; margin-top: 4px; display: block; }
        .success-box { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 15px; }
        button { background: #2563eb; color: white; border: none; padding: 12px 20px; font-size: 15px; font-weight: 600; border-radius: 8px; cursor: pointer; width: 100%; transition: background 0.2s; }
        button:hover { background: #1d4ed8; }
        .hint { font-size: 12px; color: #64748b; margin-top: 4px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Додати нову транзакцію</h1>
        <p class="subtitle">Практична робота №2 · Варіант 13 (Трекер особистих витрат)</p>

        <?php if (!empty($successMessage)): ?>
            <div class="success-box">
                <strong><?= htmlspecialchars($successMessage) ?></strong><br>
                Сума: <strong><?= htmlspecialchars($amount) ?> грн</strong> | 
                Категорія: <strong><?= htmlspecialchars($category) ?></strong> | 
                Дата: <strong><?= htmlspecialchars($date) ?></strong>
            </div>
        <?php endif; ?>

        <!-- Крок 2 & 4: Форма з виводом помилок та збереженням введених даних через htmlspecialchars -->
        <form id="expenseForm" method="post" action="form.php" novalidate>
            <div class="form-group">
                <label for="amount">Сума (грн):</label>
                <input type="number" step="0.01" id="amount" name="amount" value="<?= htmlspecialchars($amount) ?>" required>
                <?php if (isset($errors['amount'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['amount']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Категорія:</label>
                <select id="category" name="category" required>
                    <option value="">-- Оберіть категорію --</option>
                    <?php foreach ($allowedCategories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= ($category === $cat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="hint">Остання обрана категорія зберігається в браузері (localStorage)</span>
                <?php if (isset($errors['category'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['category']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="date">Дата транзакції:</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($date ?: date('Y-m-d')) ?>" required>
                <?php if (isset($errors['date'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['date']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit">Зберегти транзакцію</button>
        </form>
    </div>

    <!-- Крок 5 & 6: Клієнтська валідація та сценарій localStorage для Варіанта 13 -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('expenseForm');
            const categorySelect = document.getElementById('category');
            const amountInput = document.getElementById('amount');

            // Крок 6: Відновлення останньої обраної категорії з localStorage
            const savedCategory = localStorage.getItem('last_expense_category');
            if (savedCategory && !categorySelect.value) {
                categorySelect.value = savedCategory;
            }

            // Збереження обраної категорії в localStorage при зміні
            categorySelect.addEventListener('change', () => {
                localStorage.setItem('last_expense_category', categorySelect.value);
            });

            // Крок 5: Клієнтська JavaScript-валідація перед відправкою форми
            form.addEventListener('submit', (event) => {
                const amountVal = parseFloat(amountInput.value);
                
                if (isNaN(amountVal) || amountVal <= 0) {
                    alert('Помилка (JS): Сума витрати має бути більшою за 0!');
                    amountInput.focus();
                    event.preventDefault(); // Зупиняємо відправку форми
                }
            });
        });
    </script>
</body>
</html>