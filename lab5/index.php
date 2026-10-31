<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Трекер витрат — Практична №5 (AJAX / JSON)</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f6f9; color: #2c3e50; padding: 32px 16px; line-height: 1.6; }
        .container { max-width: 850px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        h1 { font-size: 24px; margin-bottom: 8px; color: #1a252f; }
        .subtitle { color: #6c757d; font-size: 14px; margin-bottom: 24px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        @media(max-width: 768px) { .grid { grid-template-columns: 1fr; } }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; }
        .card h3 { font-size: 14px; color: #64748b; text-transform: uppercase; margin-bottom: 12px; }
        .balance-val { font-size: 24px; font-weight: 700; color: #2563eb; }
        .form-group { margin-bottom: 12px; }
        label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; }
        input, select { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        .btn { background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%; font-size: 14px; }
        .btn:hover { background: #1d4ed8; }
        .search-box { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
        .error-msg { color: #dc2626; font-size: 13px; margin-top: 8px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Трекер особистих витрат (AJAX & JSON)</h1>
        <p class="subtitle">Практична робота №5 · Варіант 13</p>

        <div class="grid">
            <!-- Блок балансу -->
            <div class="card">
                <h3>Загальний баланс витрат</h3>
                <div class="balance-val" id="totalBalance">0.00 грн</div>
            </div>

            <!-- Форма додавання через AJAX -->
            <div class="card">
                <h3>Додати транзакцію</h3>
                <form id="addForm">
                    <div class="form-group">
                        <label>Сума (грн):</label>
                        <input type="number" step="0.01" name="amount" id="amountInput" required>
                    </div>
                    <div class="form-group">
                        <label>Категорія:</label>
                        <input type="text" name="category" id="categoryInput" placeholder="Напр. Продукти" required>
                    </div>
                    <div class="form-group">
                        <label>Дата:</label>
                        <input type="date" name="transaction_date" id="dateInput" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="submit" class="btn">Додати асинхронно</button>
                    <span id="formError" class="error-msg"></span>
                </form>
            </div>
        </div>

        <!-- Пошук -->
        <div class="search-box">
            <label for="searchInput" style="font-weight: 600; font-size: 14px; margin-bottom: 6px; display: block;">Живий пошук за категорією:</label>
            <input type="text" id="searchInput" placeholder="Введіть категорію для фільтрації...">
        </div>

        <h2>Список транзакцій</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Сума</th>
                    <th>Категорія</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody id="resultsTableBody">
                <tr><td colspan="4" style="text-align: center; color: #64748b;">Завантаження даних...</td></tr>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const resultsBody = document.getElementById('resultsTableBody');
            const totalBalanceEl = document.getElementById('totalBalance');
            const addForm = document.getElementById('addForm');
            const formError = document.getElementById('formError');

            // Функція завантаження та рендерингу списку транзакцій через fetch
            async function loadTransactions(query = '') {
                try {
                    const response = await fetch(`api_list.php?q=${encodeURIComponent(query)}`);
                    if (!response.ok) throw new Error('Помолка завантаження даних із сервера');

                    const result = await response.json();
                    
                    if (!result.success) throw new Error(result.error || 'Помилка API');

                    // Оновлюємо загальний баланс
                    totalBalanceEl.textContent = Number(result.balance).toLocaleString('uk-UA', { minimumFractionDigits: 2 }) + ' грн';

                    // Рендеримо таблицю
                    renderTable(result.data);
                } catch (error) {
                    console.error(error);
                    resultsBody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: #dc2626;">Помилка: ${error.message}</td></tr>`;
                }
            }

            // Рендер рядків таблиці з захистом від XSS через textContent
            function renderTable(data) {
                resultsBody.innerHTML = '';
                if (data.length === 0) {
                    resultsBody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: #64748b;">Транзакцій не знайдено.</td></tr>`;
                    return;
                }

                data.forEach(tx => {
                    const tr = document.createElement('tr');

                    const tdId = document.createElement('td');
                    tdId.textContent = tx.id;

                    const tdAmount = document.createElement('td');
                    tdAmount.innerHTML = `<strong>${Number(tx.amount).toLocaleString('uk-UA', { minimumFractionDigits: 2 })} грн</strong>`;

                    const tdCategory = document.createElement('td');
                    tdCategory.textContent = tx.category;

                    const tdDate = document.createElement('td');
                    tdDate.textContent = tx.transaction_date;

                    tr.appendChild(tdId);
                    tr.appendChild(tdAmount);
                    tr.appendChild(tdCategory);
                    tr.appendChild(tdDate);

                    resultsBody.appendChild(tr);
                });
            }

            // Початкове завантаження
            loadTransactions();

            // Живий пошук на подію input
            searchInput.addEventListener('input', (e) => {
                loadTransactions(e.target.value.trim());
            });

            // Обробка відправлення форми через AJAX (POST)
            addForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                formError.textContent = '';

                const formData = new FormData(addForm);

                try {
                    const response = await fetch('api_add.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.error || 'Не вдалося додати транзакцію');
                    }

                    // Очищаємо форму
                    addForm.reset();
                    document.getElementById('dateInput').value = new Date().toISOString().split('T')[0];

                    // Оновлюємо список і баланс без перезавантаження сторінки
                    loadTransactions(searchInput.value.trim());

                } catch (error) {
                    console.error(error);
                    formError.textContent = error.message;
                }
            });
        });
    </script>
</body>
</html>