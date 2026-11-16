<?php
// Funcionalidade 5 — listagem dos produtos salvos
require 'conexao.php';

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function reais($v) { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }

$busca = trim($_GET['busca'] ?? '');
$msg   = $_GET['msg'] ?? null;

// Desafio 2 — pesquisa local por nome
if ($busca !== '') {
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE nome LIKE ? ORDER BY data_cadastro DESC');
    $stmt->execute(['%' . $busca . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM produtos ORDER BY data_cadastro DESC');
}
$produtos = $stmt->fetchAll();

// Desafio 4 — dashboard
$dash = $pdo->query(
    'SELECT COUNT(*) AS total,
            COALESCE(AVG(preco), 0) AS media,
            COALESCE(SUM(estoque), 0) AS estoque
     FROM produtos'
)->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos Cadastrados</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Catálogo de Produtos</h1>
    <nav><a href="index.php">Consulta</a> <a href="produtos.php" class="ativo">Produtos salvos</a></nav>
</header>

<main>
    <section class="dashboard">
        <div class="metrica"><span>Produtos cadastrados</span><strong><?= (int)$dash['total'] ?></strong></div>
        <div class="metrica"><span>Valor médio dos produtos</span><strong><?= reais($dash['media']) ?></strong></div>
        <div class="metrica"><span>Produtos em estoque</span><strong><?= (int)$dash['estoque'] ?></strong></div>
    </section>

    <?php if ($msg): ?>
        <p class="alerta sucesso"><?= e($msg) ?></p>
    <?php endif; ?>

    <section class="card">
        <form method="get" action="produtos.php" class="linha">
            <input type="text" name="busca" placeholder="Pesquisar produto pelo nome" value="<?= e($busca) ?>">
            <button type="submit">Pesquisar</button>
            <?php if ($busca !== ''): ?><a class="botao secundario" href="produtos.php">Limpar</a><?php endif; ?>
        </form>
    </section>

    <section class="card">
        <?php if (!$produtos): ?>
            <p class="vazio"><?= $busca !== '' ? 'Nenhum produto encontrado para "' . e($busca) . '".' : 'Nenhum produto cadastrado ainda.' ?></p>
        <?php else: ?>
            <div class="tabela-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th><th></th><th>Nome</th><th>Preço</th><th>Categoria</th>
                        <th>Estoque</th><th>Avaliação</th><th>Cadastro</th><th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= (int)$p['id'] ?></td>
                            <td><?php if ($p['imagem']): ?><img class="mini" src="<?= e($p['imagem']) ?>" alt=""><?php endif; ?></td>
                            <td><?= e($p['nome']) ?><br><small>ID API: <?= (int)$p['id_api'] ?></small></td>
                            <td><?= reais($p['preco']) ?></td>
                            <td><?= e($p['categoria']) ?></td>
                            <td><?= (int)$p['estoque'] ?></td>
                            <td><?= number_format((float)$p['avaliacao'], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['data_cadastro'])) ?></td>
                            <td>
                                <form method="post" action="excluir.php" class="form-excluir">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <button type="submit" class="perigo">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <p><a class="botao" href="index.php">← Voltar para consulta</a></p>
</main>

<script src="js/script.js"></script>
</body>
</html>
