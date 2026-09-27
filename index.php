<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/db.php';

$status = $_GET['status'] ?? '';
$q = trim($_GET['q'] ?? '');

/*
|--------------------------------------------------------------------------
| READ
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query(
    "SELECT id, name, category, price, stock, created_at
     FROM products
     ORDER BY id DESC"
);

$products = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
| Search dilakukan di PHP supaya tidak menggunakan parameter PDO
| untuk fitur bonus search.
|--------------------------------------------------------------------------
*/
if ($q !== '') {
    $keyword = mb_strtolower($q);

    $products = array_filter(
        $products,
        function (array $product) use ($keyword): bool {
            $name = mb_strtolower(
                (string)($product['name'] ?? '')
            );

            $category = mb_strtolower(
                (string)($product['category'] ?? '')
            );

            return str_contains($name, $keyword)
                || str_contains($category, $keyword);
        }
    );
}

/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/
function e($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function rupiah($value): string
{
    return 'Rp ' . number_format(
        (float)$value,
        0,
        ',',
        '.'
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Product Manager</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >
</head>

<body>

<div class="container">

    <header class="header">

        <div>
            <p class="eyebrow">
                PHP + MySQL
            </p>

            <h1>
                Product Manager
            </h1>

            <p class="subtitle">
                Kelola data produk dengan CRUD
                yang aman dan responsif.
            </p>
        </div>

        <a
            class="btn btn-primary"
            href="create.php"
        >
            + Tambah Produk
        </a>

    </header>


    <?php if ($status === 'created'): ?>

        <div class="alert success">
            Produk berhasil ditambahkan.
        </div>

    <?php elseif ($status === 'updated'): ?>

        <div class="alert success">
            Produk berhasil diperbarui.
        </div>

    <?php elseif ($status === 'deleted'): ?>

        <div class="alert success">
            Produk berhasil dihapus.
        </div>

    <?php endif; ?>


    <!-- SEARCH -->

    <section class="toolbar card">

        <form
            method="GET"
            class="search-form"
        >

            <label for="q">
                Cari produk
            </label>

            <div class="search-row">

                <input
                    id="q"
                    name="q"
                    value="<?= e($q) ?>"
                    placeholder="Nama atau kategori..."
                >

                <button
                    class="btn btn-secondary"
                    type="submit"
                >
                    Cari
                </button>

                <?php if ($q !== ''): ?>

                    <a
                        class="btn btn-light"
                        href="index.php"
                    >
                        Reset
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </section>


    <!-- PRODUCT LIST -->

    <section class="products">

        <?php if (empty($products)): ?>

            <div class="empty card">

                <h2>
                    Produk tidak ditemukan
                </h2>

                <p>
                    Belum ada produk yang sesuai
                    dengan pencarian.
                </p>

            </div>

        <?php endif; ?>


        <?php foreach ($products as $p): ?>

            <article class="product-card card">

                <div class="product-top">

                    <span class="badge">
                        <?= e($p['category']) ?>
                    </span>

                    <span
                        class="stock <?= (int)$p['stock'] === 0 ? 'out' : '' ?>"
                    >
                        Stok:
                        <?= (int)$p['stock'] ?>
                    </span>

                </div>


                <h2>
                    <?= e($p['name']) ?>
                </h2>


                <div class="price">
                    <?= rupiah($p['price']) ?>
                </div>


                <p class="date">

                    Ditambahkan:

                    <?= e(
                        date(
                            'd/m/Y H:i',
                            strtotime($p['created_at'])
                        )
                    ) ?>

                </p>


                <div class="actions">

                    <a
                        class="btn btn-secondary"
                        href="edit.php?id=<?= (int)$p['id'] ?>"
                    >
                        Edit
                    </a>


                    <form
                        method="POST"
                        action="delete.php"
                        onsubmit="return confirm('Yakin ingin menghapus produk ini?');"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int)$p['id'] ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf"
                            value="<?= e($_SESSION['csrf'] ?? '') ?>"
                        >

                        <button
                            class="btn btn-danger"
                            type="submit"
                        >
                            Hapus
                        </button>

                    </form>

                </div>

            </article>

        <?php endforeach; ?>

    </section>

</div>

</body>
</html>