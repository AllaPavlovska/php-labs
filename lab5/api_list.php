<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');

try {
    if ($query !== '') {
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE category LIKE :q ORDER BY transaction_date DESC, id DESC");
        $stmt->execute([':q' => "%$query%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM transactions ORDER BY transaction_date DESC, id DESC");
    }
    
    $transactions = $stmt->fetchAll();

    // Розрахунок загального балансу для передачі на клієнт
    $balanceStmt = $pdo->query("SELECT SUM(amount) AS balance FROM transactions");
    $balanceRow = $balanceStmt->fetch();
    $totalBalance = $balanceRow['balance'] ?? 0.00;

    echo json_encode([
        'success' => true,
        'balance' => (float)$totalBalance,
        'data' => $transactions
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}