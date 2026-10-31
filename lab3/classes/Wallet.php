<?php
declare(strict_types=1);

class Wallet {
    /** @var Transaction[] */
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void {
        $this->transactions[] = $transaction;
    }

    public function getTransactions(): array {
        return $this->transactions;
    }

    // Метод агрегації: загальна сума витрат за конкретною категорією
    public function totalByCategory(string $category): float {
        $total = 0.0;
        foreach ($this->transactions as $tx) {
            if ($tx->getCategory() === $category) {
                $total += $tx->getAmount();
            }
        }
        return $total;
    }

    // Метод розрахунку загального балансу (суми витрат)
    public function balance(): float {
        $sum = 0.0;
        foreach ($this->transactions as $tx) {
            $sum += $tx->getAmount();
        }
        return $sum;
    }
}