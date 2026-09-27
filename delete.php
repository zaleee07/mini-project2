<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method tidak diizinkan.');
}

$sessionToken = $_SESSION['csrf'] ?? '';
$formToken = $_POST['csrf'] ?? '';

if (!$sessionToken || !$formToken || !hash_equals($sessionToken, $formToken)) {
    http_response_code(403);
    exit('Token CSRF tidak valid.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID produk tidak valid.');
}

$stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);

header('Location: index.php?status=deleted');
exit;
