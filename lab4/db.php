<?php
declare(strict_types=1);

$dbPath = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO("sqlite:$dbPath", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Автоматично створюємо таблицю transactions у файлі SQLite
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        amount DECIMAL(10, 2) NOT NULL,
        category TEXT NOT NULL,
        transaction_date TEXT NOT NULL
    )");

} catch (PDOException $e) {
    die("Помилка підключення до SQLite: " . $e->getMessage());
}