<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Shoggun's Supper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Manrope:wght@300;400;600&family=Noto+Serif+JP:wght@400;700&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.29/bundled/lenis.min.js"></script>

    <style>
        :root { --bg-color: #F0EFEB; --accent: #9A3B3B; }
        body { background-color: var(--bg-color); color: #1a1a1a; font-family: 'Manrope', sans-serif; }
        .serif { font-family: 'Cinzel', serif; }
        .menu-card { background: rgba(255,255,255,0.4); transition: all 0.4s ease; border: 1px solid transparent; }
        .menu-card:hover { background: white; border-color: var(--accent); transform: translateY(-5px); }
        
        #cart-panel { transform: translateX(100%); transition: transform 0.5s cubic-bezier(0.77, 0, 0.175, 1); }
        #cart-panel.open { transform: translateX(0); }
    </style>
</head>
<body class="p-6 md:p-12">

    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="index.php" class="text-gray-500 text-xs tracking-widest uppercase hover:text-red-900 transition">
            &larr; Return
        </a>
        <button onclick="toggleCart()" class="relative bg-black text-white px-4 py-2 text-xs tracking-widest uppercase flex items-center gap-2 w-full sm:w-auto justify-center">
            <span class="sr-only">Open Order</span>
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 12c2.5-2 4.5-2 7 0s4.5 2 7 0 4.5-2 7 0"/>
                <path d="M4 14h16l-1.2 4.2A2 2 0 0 1 16.9 20H7.1a2 2 0 0 1-1.9-1.8L4 14Z"/>
                <path d="M7 9V4m5 5V4m5 5V4"/>
            </svg>
            <span>Order</span>
            <span class="absolute -top-2 -right-2 bg-white text-black text-[10px] px-1.5 py-0.5 rounded-full border border-black" id="cart-count">0</span>
        </button>
    </div>

    <div class="max-w-6xl mx-auto mt-10 md:mt-24">
        <div class="text-center mb-20">
            <h1 class="serif text-5xl md:text-7xl mb-4">THE MENU</h1>
            <p class="text-xs uppercase tracking-[0.3em] text-gray-500">Selection of 8 Signature Bowls</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-32" id="menu-grid">
            
            <?php
            $ramens = [
                ["id" => 1, "name" => "Tensai Shio", "price" => 1200, "desc" => "Mongolian rock salt, kelp base, truffle oil.", "img" => "https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=600"],
                ["id" => 2, "name" => "Rich Tonkotsu", "price" => 1100, "desc" => "72-hr pork emulsion, chashu, black garlic oil.", "img" => "https://images.unsplash.com/photo-1591814468924-caf88d1232e1?q=80&w=600"],
                ["id" => 3, "name" => "Spicy Miso", "price" => 1350, "desc" => "Hokkaido miso blend, house chili rayu, butter corn.", "img" => "https://dishingouthealth.com/wp-content/uploads/2022/01/SpicyMisoRamen_Square.jpg"],
                ["id" => 4, "name" => "Wagyu Shoyu", "price" => 2500, "desc" => "A5 Miyazaki Wagyu slice, aged soy broth.", "img" => "https://img.freepik.com/premium-photo/rare-slice-a5-wagyu-with-minced-scallion-daikon-shoyu-sauce_43263-1463.jpg"],
                ["id" => 5, "name" => "Black Garlic Ramen", "price" => 1250, "desc" => "Fermented garlic oil, toasted sesame, double chashu.", "img" => "https://images.pexels.com/photos/884600/pexels-photo-884600.jpeg?auto=compress&cs=tinysrgb&w=600"],
                ["id" => 6, "name" => "Yuzu Shio", "price" => 1400, "desc" => "Clear chicken dashi with Japanese citron zest.", "img" => "https://images.pexels.com/photos/1907229/pexels-photo-1907229.jpeg?auto=compress&cs=tinysrgb&w=600"],
                ["id" => 7, "name" => "Vegan Tantanmen", "price" => 1300, "desc" => "Soy milk broth, spicy soy crumbles, bok choy.", "img" => "https://images.pexels.com/photos/2664216/pexels-photo-2664216.jpeg?auto=compress&cs=tinysrgb&w=600"],
                ["id" => 8, "name" => "Truffle Shoyu", "price" => 1800, "desc" => "Porcini infused soy broth with shaved black truffle.", "img" => "https://images.pexels.com/photos/11213749/pexels-photo-11213749.jpeg?auto=compress&cs=tinysrgb&w=600"]
            ];

            foreach($ramens as $r): ?>
                <div class="menu-card p-6 flex gap-6 items-center rounded-sm opacity-0 translate-y-10">
                    <img src="<?= $r['img'] ?>" class="w-32 h-32 object-cover rounded-sm shadow-md">
                    <div class="flex-1">
                        <h3 class="serif text-xl mb-2"><?= $r['name'] ?></h3>
                        <p class="text-xs text-gray-500 mb-2"><?= $r['desc'] ?></p>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-2">
                            <div class="inline-block text-[10px] font-bold text-[#7A2E2E] bg-[#F3ECE7] px-2.5 py-1 rounded-full tracking-widest">¥<?= $r['price'] ?></div>
                            <button onclick="addToCart('<?= $r['name'] ?>', <?= $r['price'] ?>)" class="text-[10px] uppercase tracking-widest border border-black px-4 py-2 hover:bg-black hover:text-white transition">
                                Add to Order
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="cart-panel" class="fixed top-0 right-0 w-full md:w-[400px] h-full bg-white z-[100] shadow-2xl p-10 flex flex-col">
        <div class="flex justify-between items-center mb-10">
            <h2 class="serif text-3xl">YOUR ORDER</h2>
            <button onclick="toggleCart()" class="text-xs uppercase opacity-50">Close</button>
        </div>
        
        <div id="cart-items" class="flex-1 overflow-y-auto space-y-6">
            <p class="text-gray-400 italic text-sm">Your bowl is currently empty...</p>
        </div>

        <div class="border-t border-gray-200 pt-6 mt-6">
            <div class="flex justify-between mb-6">
                <span class="serif text-xl">Total</span>
                <span id="total-price" class="serif text-xl text-[#9A3B3B]">¥0</span>
            </div>
            <button id="send-order-btn" onclick="sendOrder()" class="w-full bg-[#9A3B3B] text-white py-4 text-xs tracking-widest uppercase hover:bg-black transition disabled:opacity-50">
                Place Order
            </button>
        </div>
    </div>

    <div id="modal-backdrop" class="fixed inset-0 bg-black/50 z-[200] hidden"></div>
    <div id="confirm-modal" class="fixed inset-0 z-[210] hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-md p-8 shadow-2xl border border-black">
            <div class="serif text-2xl mb-2" id="modal-title">Order Sent</div>
            <div class="text-sm text-gray-600 mb-6" id="modal-message">Your order has been sent to the kitchen.</div>
            <div class="flex justify-end gap-3">
                <button id="modal-invoice" class="text-xs uppercase tracking-widest px-4 py-2 border border-black hover:bg-black hover:text-white transition">View Invoice</button>
                <button id="modal-close" class="text-xs uppercase tracking-widest px-4 py-2 border border-black hover:bg-black hover:text-white transition">Close</button>
            </div>
        </div>
    </div>

    <script>
        const lenis = new Lenis();
        function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
        requestAnimationFrame(raf);

        gsap.to(".menu-card", {
            opacity: 1, y: 0, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.3
        });

        let cart = [];
        let lastOrder = [];
        
        function toggleCart() {
            document.getElementById('cart-panel').classList.toggle('open');
        }

        function addToCart(name, price) {
            const existing = cart.find(item => item.name === name);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ name, price, qty: 1 });
            }
            updateUI();
        }

        function updateUI() {
            const list = document.getElementById('cart-items');
            const count = document.getElementById('cart-count');
            const total = document.getElementById('total-price');
            
            count.innerText = cart.length;
            list.innerHTML = cart.map((item, index) => `
                <div class="flex justify-between items-center animate-fade-in">
                    <div>
                        <p class="font-bold text-sm">${item.name}</p>
                        <p class="text-[10px] text-gray-400">¥${item.price} × ${item.qty}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="decreaseQty(${index})" class="text-xs border border-black px-2">-</button>
                        <span class="text-xs w-4 text-center">${item.qty}</span>
                        <button onclick="increaseQty(${index})" class="text-xs border border-black px-2">+</button>
                        <button onclick="removeFromCart(${index})" class="text-red-800 text-xs ml-2">remove</button>
                    </div>
                </div>
            `).join('');

            const sum = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            total.innerText = `¥${sum}`;
            document.getElementById('send-order-btn').disabled = cart.length === 0;
        }

        function increaseQty(index) {
            cart[index].qty += 1;
            updateUI();
        }

        function decreaseQty(index) {
            cart[index].qty -= 1;
            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }
            updateUI();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateUI();
        }

        const wsProtocol = location.protocol === 'https:' ? 'wss' : 'ws';
        const wsHost = 'php-websocket-server.onrender.com';
        const wsToken = 'changeme';
        const socket = new WebSocket(`${wsProtocol}://${wsHost}?token=${encodeURIComponent(wsToken)}`);

        socket.onopen = () => console.log('Connected to Ramen Kitchen Socket');
        socket.onclose = () => {
            showModal('Kitchen Offline', 'WebSocket connection closed. Check your token and server status.');
        };
        socket.onerror = () => {
            showModal('Kitchen Offline', 'WebSocket error. Check your token and server status.');
        };
        socket.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            if (msg.type === 'confirm') {
                showModal('Order Confirmed', 'Kitchen confirmed your order!');
            }
            if (msg.type === 'error') {
                showModal('Kitchen Error', msg.message || 'Unauthorized or connection error.');
            }
        };

        function showModal(title, message) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-message').textContent = message;
            document.getElementById('modal-backdrop').classList.remove('hidden');
            const modal = document.getElementById('confirm-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function showModalHtml(title, html) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-message').innerHTML = html;
            document.getElementById('modal-backdrop').classList.remove('hidden');
            const modal = document.getElementById('confirm-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideModal() {
            document.getElementById('modal-backdrop').classList.add('hidden');
            const modal = document.getElementById('confirm-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('modal-close').addEventListener('click', hideModal);
        document.getElementById('modal-backdrop').addEventListener('click', hideModal);
        document.getElementById('modal-invoice').addEventListener('click', () => {
            if (lastOrder.length === 0) {
                showModal('No Recent Order', 'You have no recent order to invoice yet.');
                return;
            }
            const total = lastOrder.reduce((acc, item) => acc + (item.price * item.qty), 0);
            const rows = lastOrder.map(item => `
                <div class=\"flex justify-between text-sm\">
                    <span>${item.name} × ${item.qty}</span>
                    <span>¥${item.price * item.qty}</span>
                </div>
            `).join('');

            showModalHtml('Invoice', `
                <div class=\"space-y-2 mb-4\">${rows}</div>
                <div class=\"border-t border-gray-200 pt-3 flex justify-between font-semibold\">
                    <span>Total</span>
                    <span>¥${total}</span>
                </div>
            `);
        });

        function sendOrder() {
            if (socket.readyState === WebSocket.OPEN) {
                const orderData = {
                    type: 'new_order',
                    timestamp: new Date().getTime(),
                    items: cart,
                    table: "Table 08",
                    total: cart.reduce((acc, item) => acc + (item.price * item.qty), 0)
                };

                socket.send(JSON.stringify(orderData));

                showModal('Order Sent', 'Your order has been sent to the kitchen.');
                lastOrder = cart.map(item => ({ ...item }));
                cart = [];
                updateUI();
            } else {
                showModal('Kitchen Offline', 'Kitchen is currently offline. Please check your WebSocket server.');
            }
        }
    </script>
</body>
</html>
