<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Читаємо дані з POST або JSON-тіла запиту
$amount = trim($_POST['amount'] ?? '');
$category = trim($_POST['category'] ?? '');
$date = trim($_POST['transaction_date'] ?? '');

if ($amount === '' || !is_numeric($amount) || (float)$amount <= 0 || $category === '' || $date === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Некоректні дані у формі.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (amount, category, transaction_date) VALUES (:amount, :category, :date)");
    $stmt->execute([
        ':amount' => $amount,
        ':category' => $category,
        ':date' => $date
    ]);

    $newId = $pdo->lastInsertId();

    // Повертаємо успішну відповідь з кодом 201
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'id' => $newId,
        'message' => 'Транзакцію успішно додано'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}