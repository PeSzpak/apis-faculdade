<?php
// Funcionalidade 4 — salvar produto no MySQL
require 'conexao.php';

function voltar($msg, $tipo, $codigo = '') {
    $qs = http_build_query(['codigo' => $codigo, 'msg' => $msg, 'tipo' => $tipo]);
    header("Location: index.php?$qs");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Não confiar cegamente nos dados: validar e normalizar
$idApi     = filter_input(INPUT_POST, 'id_api', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$nome      = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$preco     = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
$categoria = trim($_POST['categoria'] ?? '');
$avaliacao = filter_input(INPUT_POST, 'avaliacao', FILTER_VALIDATE_FLOAT);
$estoque   = filter_input(INPUT_POST, 'estoque', FILTER_VALIDATE_INT);
$imagem    = trim($_POST['imagem'] ?? '');

if (!$idApi || $nome === '') {
    voltar('Dados do produto inválidos. Consulte o produto novamente.', 'erro');
}
if ($imagem !== '' && !filter_var($imagem, FILTER_VALIDATE_URL)) {
    $imagem = '';
}

// Desafio 1 — evitar duplicidade
$stmt = $pdo->prepare('SELECT id FROM produtos WHERE id_api = ?');
$stmt->execute([$idApi]);
if ($stmt->fetch()) {
    voltar("O produto \"$nome\" já está cadastrado.", 'erro', $idApi);
}

$stmt = $pdo->prepare(
    'INSERT INTO produtos (id_api, nome, descricao, preco, categoria, avaliacao, estoque, imagem)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $idApi,
    mb_substr($nome, 0, 200),
    $descricao,
    $preco === false ? null : $preco,
    mb_substr($categoria, 0, 100),
    $avaliacao === false ? null : $avaliacao,
    $estoque === false ? null : $estoque,
    mb_substr($imagem, 0, 500),
]);

voltar("Produto \"$nome\" salvo com sucesso!", 'sucesso', $idApi);
