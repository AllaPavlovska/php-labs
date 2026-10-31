<?php
declare(strict_types=1);

// Форматування грошових сум
function formatMoney(float $amount): string {
    return number_format($amount, 2, ',', ' ') . ' грн';
}

// Отримання назви місяця українською за числовим номером
function monthName(string $dateString): string {
    $timestamp = strtotime($dateString);
    $months = [
        1 => 'січня', 2 => 'лютого', 3 => 'березня', 4 => 'квітня',
        5 => 'травня', 6 => 'червня', 7 => 'липня', 8 => 'серпня',
        9 => 'вересня', 10 => 'жовтня', 11 => 'листопада', 12 => 'грудня'
    ];
    $monthNum = (int)date('n', $timestamp);
    $day = date('j', $timestamp);
    $year = date('Y', $timestamp);
    
    return "{$day} " . ($months[$monthNum] ?? '') . " {$year} р.";
}