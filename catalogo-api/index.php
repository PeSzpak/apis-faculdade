<?php
// Funcionalidade 1, 2 e 3 — consulta, apresentação e tratamento de erros
$codigo  = trim($_GET['codigo'] ?? '');
$produto = null;
$erro    = null;
$aviso   = $_GET['msg'] ?? null;   // mensagem vinda do salvar.php
$tipoMsg = $_GET['tipo'] ?? 'sucesso';

if (isset($_GET['codigo'])) {
    if ($codigo === '') {
        // Situação 1 — código não informado
        $erro = 'Informe o código do produto.';
    } elseif (!ctype_digit($codigo) || (int)$codigo < 1) {
        $erro = 'O código deve ser um número inteiro positivo.';
    } else {
        // Requisição HTTP para a API REST
        $url = 'https://dummyjson.com/products/' . (int)$codigo;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);
        $resposta = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resposta === false || $status === 0 || $status >= 500) {
            // Situação 3 — falha de comunicação com a API
            $erro = 'Não foi possível realizar a consulta. Verifique sua conexão ou tente novamente mais tarde.';
        } elseif ($status === 404) {
            // Situação 2 — produto não existe
            $erro = "Produto com código $codigo não encontrado.";
        } else {
            // Interpretação do JSON recebido
            $dados = json_decode($resposta, true);
            if (!is_array($dados) || !isset($dados['id'], $dados['title'])) {
                $erro = 'A API retornou dados inválidos. Não foi possível realizar a consulta.';
            } else {
                $produto = [
                    'id_api'    => (int)$dados['id'],
                    'nome'      => (string)$dados['title'],
                    'descricao' => (string)($dados['description'] ?? ''),
                    'preco'     => (float)($dados['price'] ?? 0),
                    'categoria' => (string)($dados['category'] ?? ''),
                    'avaliacao' => (float)($dados['rating'] ?? 0),
                    'estoque'   => (int)($dados['stock'] ?? 0),
                    'imagem'    => (string)($dados['thumbnail'] ?? ($dados['images'][0] ?? '')),
                ];
            }
        }
    }
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Produtos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Catálogo de Produtos</h1>
    <nav><a href="index.php" class="ativo">Consulta</a> <a href="produtos.php">Produtos salvos</a></nav>
</header>

<main>
    <section class="card">
        <h2>Consultar produto</h2>
        <form id="form-consulta" method="get" action="index.php" novalidate>
            <label for="codigo">Código do produto</label>
            <div class="linha">
                <input type="number" id="codigo" name="codigo" min="1" placeholder="Ex.: 1" value="<?= e($codigo) ?>">
                <button type="submit">Consultar Produto</button>
            </div>
        </form>
        <p id="erro-js" class="alerta erro oculto"></p>
    </section>

    <?php if ($aviso): ?>
        <p class="alerta <?= $tipoMsg === 'erro' ? 'erro' : 'sucesso' ?>"><?= e($aviso) ?></p>
    <?php endif; ?>

    <section id="resultado">
        <?php if ($erro): ?>
            <p class="alerta erro"><?= e($erro) ?></p>
        <?php elseif ($produto): ?>
            <article class="card produto">
                <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>">
                <div class="info">
                    <span class="tag"><?= e($produto['categoria']) ?></span>
                    <h2><?= e($produto['nome']) ?></h2>
                    <p class="descricao"><?= e($produto['descricao']) ?></p>
                    <dl>
                        <div><dt>ID</dt><dd><?= e($produto['id_api']) ?></dd></div>
                        <div><dt>Preço</dt><dd>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></dd></div>
                        <div><dt>Avaliação</dt><dd>⭐ <?= number_format($produto['avaliacao'], 2, ',', '.') ?></dd></div>
                        <div><dt>Estoque</dt><dd><?= e($produto['estoque']) ?> un.</dd></div>
                    </dl>

                    <form method="post" action="salvar.php">
                        <?php foreach ($produto as $campo => $valor): ?>
                            <input type="hidden" name="<?= e($campo) ?>" value="<?= e($valor) ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="secundario">Salvar Produto</button>
                    </form>
                </div>
            </article>
        <?php endif; ?>
    </section>
</main>

<script src="js/script.js"></script>
</body>
</html>
