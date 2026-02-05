<?php

declare(strict_types=1);

session_start();

$PASSWORD = '141414';
$dataFile = __DIR__ . '/../../data/menu.json';

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: menu.php');
    exit;
}

if (!isset($_SESSION['authed'])) {
    $error = '';
    if (isset($_POST['password'])) {
        if (hash_equals($PASSWORD, (string) $_POST['password'])) {
            $_SESSION['authed'] = true;
            header('Location: menu.php');
            exit;
        }
        $error = 'Invalid password.';
    }
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Menu Manager Login</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Manrope:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Manrope', sans-serif;
                background-color: #F0EFEB;
                background-image:
                    radial-gradient(circle at 20% 10%, rgba(0,0,0,0.04) 0 1px, transparent 1px),
                    radial-gradient(circle at 80% 20%, rgba(0,0,0,0.04) 0 1px, transparent 1px),
                    radial-gradient(circle at 30% 80%, rgba(0,0,0,0.04) 0 1px, transparent 1px),
                    repeating-linear-gradient(120deg, rgba(0,0,0,0.04) 0 1px, transparent 1px 18px),
                    repeating-linear-gradient(60deg, rgba(0,0,0,0.04) 0 1px, transparent 1px 18px);
                background-size: 140px 140px, 160px 160px, 180px 180px, 28px 28px, 28px 28px;
            }
            .serif { font-family: 'Cinzel', serif; }
        </style>
    </head>
    <body class="min-h-screen bg-[#F0EFEB] flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white border border-black p-8 shadow-2xl">
            <div class="mb-6">
                <div class="serif text-2xl">Menu Manager</div>
                <p class="text-xs uppercase tracking-[0.3em] text-gray-500 mt-2">Admin Login</p>
            </div>
            <?php if ($error): ?>
                <div class="bg-white border border-black p-2 text-xs uppercase tracking-widest mb-4 text-red-700"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" class="space-y-4">
                <input type="password" name="password" placeholder="Password" class="w-full border border-black px-3 py-2" required>
                <button type="submit" class="w-full bg-black text-white py-2 text-xs uppercase tracking-widest">Sign In</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$items = [];
if (is_file($dataFile)) {
    $json = file_get_contents($dataFile);
    $decoded = json_decode($json, true);
    if (is_array($decoded)) {
        $items = $decoded;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $items = [];
    $names = $_POST['name'] ?? [];
    $descs = $_POST['desc'] ?? [];
    $prices = $_POST['price'] ?? [];
    $imgs = $_POST['img'] ?? [];

    foreach ($names as $i => $name) {
        $name = trim((string) $name);
        $desc = trim((string) ($descs[$i] ?? ''));
        $price = (int) ($prices[$i] ?? 0);
        $img = trim((string) ($imgs[$i] ?? ''));

        if ($name === '' || $price <= 0) {
            continue;
        }

        $items[] = [
            'id' => $i + 1,
            'name' => $name,
            'price' => $price,
            'desc' => $desc,
            'img' => $img,
        ];
    }

    file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    header('Location: menu.php?saved=1');
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'add_item') {
    $name = trim((string) ($_POST['new_name'] ?? ''));
    $desc = trim((string) ($_POST['new_desc'] ?? ''));
    $price = (int) ($_POST['new_price'] ?? 0);
    $img = trim((string) ($_POST['new_img'] ?? ''));

    if (!empty($_FILES['new_upload']['name'])) {
        $uploadDir = __DIR__ . '/../uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $ext = pathinfo($_FILES['new_upload']['name'], PATHINFO_EXTENSION);
        $fileName = 'menu_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
        $dest = $uploadDir . '/' . $fileName;
        if (move_uploaded_file($_FILES['new_upload']['tmp_name'], $dest)) {
            $img = '/uploads/' . $fileName;
        }
    }

    if ($name !== '' && $price > 0) {
        $items[] = [
            'id' => count($items) + 1,
            'name' => $name,
            'price' => $price,
            'desc' => $desc,
            'img' => $img,
        ];
        file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        header('Location: menu.php?saved=1');
        exit;
    }
    header('Location: menu.php?error=1');
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Manrope:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .serif { font-family: 'Cinzel', serif; }
    </style>
</head>
<body class="bg-[#F0EFEB] p-6 md:p-10">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="serif text-3xl md:text-4xl">Menu Manager</h1>
                <p class="text-xs uppercase tracking-[0.3em] text-gray-500 mt-2">Name, Description, Price</p>
            </div>
            <form method="post">
                <button name="logout" class="text-xs uppercase tracking-widest px-4 py-2 border border-black">Logout</button>
            </form>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div class="bg-white border border-black p-3 text-xs uppercase tracking-widest mb-6">Saved</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-white border border-black p-3 text-xs uppercase tracking-widest mb-6 text-red-700">Name and price are required</div>
        <?php endif; ?>

        <form method="post" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <div class="flex flex-col sm:flex-row gap-3 sm:flex">
                <button type="button" id="open-add" class="text-xs uppercase tracking-widest px-4 py-2 border border-black hidden sm:inline-block">Add Item</button>
                <button type="submit" class="text-xs uppercase tracking-widest px-4 py-2 bg-black text-white hidden sm:inline-block">Save Menu</button>
            </div>
            <?php foreach ($items as $i => $item): ?>
                <details class="bg-white border border-black p-4" <?= !empty($item['_new']) ? 'open' : '' ?>>
                    <summary class="cursor-pointer text-sm uppercase tracking-widest flex items-center justify-between">
                        <span><?= htmlspecialchars($item['name'] ?: 'New Item') ?></span>
                        <?php if ((int) $item['price'] > 0): ?>
                            <span class="text-[10px] font-bold text-[#7A2E2E] bg-[#F3ECE7] px-2.5 py-1 rounded-full tracking-widest">¥<?= (int) $item['price'] ?></span>
                        <?php endif; ?>
                    </summary>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <input class="border border-black px-3 py-2" name="name[]" placeholder="Name" value="<?= htmlspecialchars($item['name']) ?>" required>
                        <input class="border border-black px-3 py-2" name="price[]" placeholder="Price" type="number" min="1" value="<?= (int) $item['price'] ?>" required>
                        <input class="border border-black px-3 py-2 md:col-span-2" name="img[]" placeholder="Image URL" value="<?= htmlspecialchars($item['img']) ?>">
                        <textarea class="border border-black px-3 py-2 md:col-span-2" name="desc[]" rows="2" placeholder="Description"><?= htmlspecialchars($item['desc']) ?></textarea>
                    </div>
                </details>
            <?php endforeach; ?>
        </form>
    </div>

    <button type="button" id="open-add-mobile" class="fixed bottom-6 right-6 z-[120] sm:hidden bg-black text-white w-14 h-14 rounded-full shadow-2xl flex items-center justify-center">
        <span class="sr-only">Add Item</span>
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 5v14M5 12h14"/>
        </svg>
    </button>

    <div id="add-backdrop" class="fixed inset-0 bg-black/50 z-[200] hidden"></div>
    <div id="add-modal" class="fixed inset-0 z-[210] hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg p-6 shadow-2xl border border-black">
            <div class="serif text-2xl mb-4">Add Menu Item</div>
            <form method="post" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="add_item">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input name="new_name" class="border border-black px-3 py-2" placeholder="Name" required>
                    <input name="new_price" class="border border-black px-3 py-2" placeholder="Price" type="number" min="1" required>
                    <input name="new_img" class="border border-black px-3 py-2 md:col-span-2" placeholder="Image URL (optional)">
                    <input name="new_upload" type="file" accept="image/*" class="border border-black px-3 py-2 md:col-span-2">
                    <textarea name="new_desc" class="border border-black px-3 py-2 md:col-span-2" rows="2" placeholder="Description"></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="add-cancel" class="text-xs uppercase tracking-widest px-4 py-2 border border-black">Cancel</button>
                    <button type="submit" class="text-xs uppercase tracking-widest px-4 py-2 bg-black text-white">Add</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const addBackdrop = document.getElementById('add-backdrop');
        const addModal = document.getElementById('add-modal');
        const openAdd = document.getElementById('open-add');
        const openAddMobile = document.getElementById('open-add-mobile');
        const addCancel = document.getElementById('add-cancel');

        function openAddModal() {
            addBackdrop.classList.remove('hidden');
            addModal.classList.remove('hidden');
            addModal.classList.add('flex');
        }

        function closeAddModal() {
            addBackdrop.classList.add('hidden');
            addModal.classList.add('hidden');
            addModal.classList.remove('flex');
        }

        openAdd.addEventListener('click', openAddModal);
        openAddMobile.addEventListener('click', openAddModal);
        addCancel.addEventListener('click', closeAddModal);
        addBackdrop.addEventListener('click', closeAddModal);
    </script>
</body>
</html>
