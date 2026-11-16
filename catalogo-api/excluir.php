<?php
// Desafio 3 — exclusão
require 'conexao.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: produtos.php?msg=' . urlencode('Produto excluído.'));
exit;
