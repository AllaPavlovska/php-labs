<?php
declare(strict_types=1);

require_once __DIR__ . '/Transaction.php';

class RecurringExpense extends Transaction {
    private string $frequency; // Наприклад: щомісяця, щотижня

    public function __construct(float $amount, string $category, string $date, string $frequency) {
        parent::__construct($amount, $category, $date);
        $this->frequency = $frequency;
    }

    public function getFrequency(): string {
        return $this->frequency;
    }

    // Перевизначення методу getInfo з викликом parent::getInfo()
    public function getInfo(): string {
        $parentInfo = parent::getInfo();
        return "{$parentInfo} | Регулярність: {$this->frequency}";
    }
}