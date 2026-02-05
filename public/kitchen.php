<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Dashboard | Tensai Ramen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Manrope:wght@300;400;600&family=Noto+Serif+JP:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-color: #F0EFEB; --accent: #9A3B3B; }
        body { background-color: var(--bg-color); color: #1a1a1a; font-family: 'Manrope', sans-serif; }
        .serif { font-family: 'Cinzel', serif; }
        .jp { font-family: 'Noto Serif JP', serif; }
        .card { background: rgba(255,255,255,0.6); border: 1px solid rgba(0,0,0,0.08); }
    </style>
</head>
<body class="p-6 md:p-12">

    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-10">
            <div>
                <div class="jp text-3xl">天才</div>
                <h1 class="serif text-4xl md:text-5xl">Kitchen Dashboard</h1>
                <p class="text-xs uppercase tracking-[0.3em] text-gray-500 mt-2">Live Orders</p>
            </div>
            <div class="flex flex-col items-end gap-3 w-full md:w-auto">
                <div id="status" class="text-[10px] uppercase tracking-[0.3em] px-2 py-1 border border-black">Disconnected</div>
                <div class="flex flex-wrap items-center justify-end gap-3 md:gap-4 w-full md:w-auto">
                    <input id="table-filter" type="text" placeholder="Filter by table (e.g. Table 08)" class="text-xs uppercase tracking-widest px-3 py-2 border border-black bg-transparent w-full md:w-56" />
                    <select id="status-filter" class="text-xs uppercase tracking-widest px-3 py-2 border border-black bg-transparent w-full md:w-auto">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="completed">Completed</option>
                    </select>
                    <button id="reset-filters" class="text-xs uppercase tracking-widest px-4 py-2 border border-black hover:bg-black hover:text-white transition w-full md:w-auto">Reset Filters</button>
                    <button id="clear" class="text-xs uppercase tracking-widest px-4 py-2 border border-black hover:bg-black hover:text-white transition w-full md:w-auto">Clear</button>
                </div>
            </div>
        </div>

        <div id="orders" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div id="empty" class="card p-6">
                <p class="text-gray-500 text-sm italic">No orders yet. Waiting for incoming orders...</p>
            </div>
        </div>
    </div>

    <script>
        const statusEl = document.getElementById('status');
        const ordersEl = document.getElementById('orders');
        const emptyEl = document.getElementById('empty');
        const clearBtn = document.getElementById('clear');
        const filterEl = document.getElementById('table-filter');
        const statusFilterEl = document.getElementById('status-filter');
        const resetFiltersBtn = document.getElementById('reset-filters');

        const wsProtocol = location.protocol === 'https:' ? 'wss' : 'ws';
        const wsHost = location.hostname || 'localhost';
        const wsToken = 'changeme';
        const wsUrl = `${wsProtocol}://${wsHost}:8080?token=${encodeURIComponent(wsToken)}`;

        const socket = new WebSocket(wsUrl);

        function setStatus(text, ok) {
            statusEl.textContent = text;
            statusEl.style.backgroundColor = ok ? '#000' : 'transparent';
            statusEl.style.color = ok ? '#fff' : '#000';
        }

        function formatTime(iso) {
            const d = new Date(iso);
            return d.toLocaleTimeString();
        }

        function renderOrder(order) {
            const card = document.createElement('div');
            card.className = 'card p-6';
            card.dataset.table = (order.table || '').toLowerCase();
            card.dataset.status = 'pending';

            const items = (order.items || []).map(i => `
                <div class="flex justify-between text-sm">
                    <span>${i.name}</span>
                    <span class="text-gray-500">¥${i.price}</span>
                </div>
            `).join('');

            card.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <div class="serif text-xl">Order #${order.from}</div>
                    <div class="text-xs uppercase tracking-widest text-gray-500">${formatTime(order.timestamp)}</div>
                </div>
                <div class="text-xs uppercase tracking-widest text-gray-500 mb-4">${order.table || 'Table'}</div>
                <div class="space-y-2">${items}</div>
                <div class="border-t border-gray-200 mt-4 pt-3 flex justify-between">
                    <span class="serif text-lg">Total</span>
                    <span class="serif text-lg text-[#9A3B3B]">¥${order.total || 0}</span>
                </div>
                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                    <button class="accept-btn text-xs uppercase tracking-widest px-4 py-2 border border-black hover:bg-black hover:text-white transition">
                        Accept
                    </button>
                    <button class="complete-btn text-xs uppercase tracking-widest px-4 py-2 border border-black hover:bg-black hover:text-white transition">
                        Complete
                    </button>
                    <span class="status text-xs uppercase tracking-widest text-gray-500 self-center">Pending</span>
                </div>
            `;

            const statusEl = card.querySelector('.status');
            const acceptBtn = card.querySelector('.accept-btn');
            const completeBtn = card.querySelector('.complete-btn');

            acceptBtn.addEventListener('click', () => {
                if (card.dataset.status !== 'pending') {
                    alert('Only pending orders can be accepted.');
                    return;
                }
                statusEl.textContent = 'Accepted';
                card.dataset.status = 'accepted';
                applyFilter();
            });
            completeBtn.addEventListener('click', () => {
                if (card.dataset.status !== 'accepted') {
                    alert('Only accepted orders can be completed.');
                    return;
                }
                statusEl.textContent = 'Completed';
                card.dataset.status = 'completed';
                applyFilter();
            });

            ordersEl.prepend(card);
            if (emptyEl) {
                emptyEl.remove();
            }

            applyFilter();
        }

        socket.addEventListener('open', () => setStatus('Connected', true));
        socket.addEventListener('close', () => setStatus('Disconnected', false));
        socket.addEventListener('error', () => setStatus('Error', false));

        socket.addEventListener('message', (event) => {
            try {
                const data = JSON.parse(event.data);
                if (data.type === 'order') {
                    renderOrder(data);
                }
            } catch {
            }
        });

        clearBtn.addEventListener('click', () => {
            ordersEl.innerHTML = '<div id="empty" class="card p-6"><p class="text-gray-500 text-sm italic">No orders yet. Waiting for incoming orders...</p></div>';
        });

        function applyFilter() {
            const q = (filterEl.value || '').trim().toLowerCase();
            const status = (statusFilterEl.value || '').trim().toLowerCase();
            const cards = ordersEl.querySelectorAll('.card');
            cards.forEach((card) => {
                if (card.id === 'empty') return;
                const tableOk = !q || card.dataset.table.includes(q);
                const statusOk = !status || card.dataset.status === status;
                card.style.display = tableOk && statusOk ? '' : 'none';
            });
        }

        filterEl.addEventListener('input', applyFilter);
        statusFilterEl.addEventListener('change', applyFilter);
        resetFiltersBtn.addEventListener('click', () => {
            filterEl.value = '';
            statusFilterEl.value = '';
            applyFilter();
        });
    </script>
</body>
</html>
