<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/db.php';

$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID produk tidak valid.');
}

$stmt = $pdo->prepare('SELECT id, name, category, price, stock FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$name = $product['name'];
$category = $product['category'];
$price = $product['price'];
$stock = $product['stock'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Umum');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama minimal 3 karakter.';
    }

    if ($category === '') {
        $category = 'Umum';
    }

    if ($price === false || $price === null || $price <= 0) {
        $errors['price'] = 'Harga harus lebih besar dari 0.';
    }

    if ($stock === false || $stock === null || $stock < 0) {
        $errors['stock'] = 'Stok tidak boleh negatif.';
    }

    if (!$errors) {
        $check = $pdo->prepare(
            'SELECT COUNT(*) FROM products WHERE name = :name AND id != :id'
        );
        $check->execute(['name' => $name, 'id' => $id]);

        if ((int)$check->fetchColumn() > 0) {
            $errors['name'] = 'Nama produk sudah digunakan.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'UPDATE products
             SET name = :name, category = :category, price = :price, stock = :stock
             WHERE id = :id'
        );
        $stmt->execute([
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'stock' => $stock,
            'id' => $id
        ]);

        header('Location: index.php?status=updated');
        exit;
    }
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container narrow">
    <a class="back-link" href="index.php">← Kembali ke daftar</a>

    <section class="form-card card">
        <p class="eyebrow">UPDATE</p>
        <h1>Edit Produk</h1>
        <p class="subtitle">Perbarui informasi produk.</p>

        <form method="POST" novalidate>
            <div class="form-group">
                <label for="name">Nama produk</label>
                <input id="name" name="name" value="<?= e((string)$name) ?>" minlength="3" required>
                <?php if (isset($errors['name'])): ?>
                    <small class="error"><?= e($errors['name']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input id="category" name="category" value="<?= e((string)$category) ?>" maxlength="50">
            </div>

            <div class="form-group">
                <label for="price">Harga</label>
                <input id="price" name="price" type="number" min="1" step="0.01"
                       value="<?= e((string)$price) ?>" required>
                <?php if (isset($errors['price'])): ?>
                    <small class="error"><?= e($errors['price']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="stock">Stok</label>
                <input id="stock" name="stock" type="number" min="0"
                       value="<?= e((string)$stock) ?>" required>
                <?php if (isset($errors['stock'])): ?>
                    <small class="error"><?= e($errors['stock']) ?></small>
                <?php endif; ?>
            </div>

            <div class="actions form-actions">
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                <a class="btn btn-light" href="index.php">Batal</a>
            </div>
        </form>
    </section>
</div>
</body>
</html>
