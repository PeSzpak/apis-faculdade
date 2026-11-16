<?php
// Atividade 02 - Formulário de cadastro de clientes
session_start();
require "consulta.php";

if (!isset($_SESSION["clientes"])) {
    $_SESSION["clientes"] = [];
}

$campos = ["nome", "cpf", "email", "telefone", "cep", "rua", "numero", "complemento", "bairro", "cidade", "uf"];
$dados = array_fill_keys($campos, "");
$erros = [];
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $acao = $_POST["acao"] ?? "";

    if ($acao == "remover") {
        unset($_SESSION["clientes"][(int) ($_POST["indice"] ?? -1)]);
        $_SESSION["clientes"] = array_values($_SESSION["clientes"]);
        $mensagem = "Cliente removido.";
    } else {
        foreach ($campos as $campo) {
            $dados[$campo] = trim($_POST[$campo] ?? "");
        }
        $dados["cpf"] = preg_replace('/\D/', '', $dados["cpf"]);
        $dados["telefone"] = preg_replace('/\D/', '', $dados["telefone"]);
        $dados["cep"] = preg_replace('/\D/', '', $dados["cep"]);
        $dados["uf"] = strtoupper($dados["uf"]);

        if ($acao == "buscar_cep") {
            // Preenche o endereço a partir do CEP
            [$endereco, $erroCep] = buscarCep($dados["cep"]);
            if ($endereco != null) {
                $dados["rua"] = $endereco->street ?? "";
                $dados["bairro"] = $endereco->neighborhood ?? "";
                $dados["cidade"] = $endereco->city ?? "";
                $dados["uf"] = $endereco->state ?? "";
            } else {
                $erros["cep"] = $erroCep;
            }
        }

        if ($acao == "salvar") {
            if ($dados["nome"] == "") $erros["nome"] = "Informe o nome.";
            if (strlen($dados["cpf"]) != 11) $erros["cpf"] = "O CPF deve ter 11 números.";
            if (!filter_var($dados["email"], FILTER_VALIDATE_EMAIL)) $erros["email"] = "E-mail inválido.";
            if (strlen($dados["telefone"]) < 10) $erros["telefone"] = "Informe DDD + número.";
            if (strlen($dados["cep"]) != 8) $erros["cep"] = "O CEP deve ter 8 números.";
            foreach (["rua" => "a rua", "numero" => "o número", "bairro" => "o bairro", "cidade" => "a cidade"] as $campo => $texto) {
                if ($dados[$campo] == "") $erros[$campo] = "Informe $texto.";
            }
            if (strlen($dados["uf"]) != 2) $erros["uf"] = "UF inválida.";

            if (empty($erros)) {
                $_SESSION["clientes"][] = $dados;
                $mensagem = "Cliente " . $dados["nome"] . " cadastrado!";
                $dados = array_fill_keys($campos, "");
            }
        }
    }
}

// Mostra o erro do campo, se houver
function erro($erros, $campo)
{
    return isset($erros[$campo]) ? '<span class="erro">' . e($erros[$campo]) . '</span>' : "";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Clientes</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #234e70;
        }

        h2 {
            color: #234e70;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .linha {
            display: flex;
            gap: 10px;
        }

        .linha > div {
            flex: 1;
        }

        .linha > div.largo {
            flex: 2;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: #234e70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #16364f;
        }

        button.secundario {
            margin-top: 5px;
            padding: 10px;
            background: #e3ebf2;
            color: #234e70;
        }

        button.remover {
            width: auto;
            margin: 0;
            padding: 6px 12px;
            background: #b00020;
        }

        .erro {
            display: block;
            color: #b00020;
            font-size: 13px;
            margin-top: 4px;
        }

        .sucesso {
            background: #e8f5e9;
            color: #1b5e20;
            padding: 12px;
            border-radius: 5px;
        }

        .cliente {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: #f7f9fb;
            padding: 12px 15px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .cliente small {
            color: #555;
        }

        a {
            color: #234e70;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Cadastro de Clientes</h1>

    <?php if ($mensagem != ""): ?>
        <p class="sucesso"><?= e($mensagem) ?></p>
    <?php endif; ?>

    <form method="POST">

        <h2>Dados pessoais</h2>

        <label>Nome completo:</label>
        <input type="text" name="nome" value="<?= e($dados["nome"]) ?>">
        <?= erro($erros, "nome") ?>

        <div class="linha">
            <div>
                <label>CPF:</label>
                <input type="text" name="cpf" placeholder="Somente números" maxlength="14" value="<?= e($dados["cpf"]) ?>">
                <?= erro($erros, "cpf") ?>
            </div>
            <div>
                <label>Telefone:</label>
                <input type="tel" name="telefone" placeholder="Ex.: 11987654321" maxlength="15" value="<?= e($dados["telefone"]) ?>">
                <?= erro($erros, "telefone") ?>
            </div>
        </div>

        <label>E-mail:</label>
        <input type="email" name="email" value="<?= e($dados["email"]) ?>">
        <?= erro($erros, "email") ?>

        <h2>Endereço</h2>

        <label>CEP:</label>
        <input type="text" name="cep" placeholder="Ex.: 01001000" maxlength="9" value="<?= e($dados["cep"]) ?>">
        <?= erro($erros, "cep") ?>
        <button type="submit" name="acao" value="buscar_cep" class="secundario">Buscar CEP e preencher endereço</button>

        <div class="linha">
            <div class="largo">
                <label>Rua:</label>
                <input type="text" name="rua" value="<?= e($dados["rua"]) ?>">
                <?= erro($erros, "rua") ?>
            </div>
            <div>
                <label>Número:</label>
                <input type="text" name="numero" maxlength="10" value="<?= e($dados["numero"]) ?>">
                <?= erro($erros, "numero") ?>
            </div>
        </div>

        <div class="linha">
            <div>
                <label>Complemento:</label>
                <input type="text" name="complemento" value="<?= e($dados["complemento"]) ?>">
            </div>
            <div>
                <label>Bairro:</label>
                <input type="text" name="bairro" value="<?= e($dados["bairro"]) ?>">
                <?= erro($erros, "bairro") ?>
            </div>
        </div>

        <div class="linha">
            <div class="largo">
                <label>Cidade:</label>
                <input type="text" name="cidade" value="<?= e($dados["cidade"]) ?>">
                <?= erro($erros, "cidade") ?>
            </div>
            <div>
                <label>UF:</label>
                <input type="text" name="uf" maxlength="2" value="<?= e($dados["uf"]) ?>">
                <?= erro($erros, "uf") ?>
            </div>
        </div>

        <button type="submit" name="acao" value="salvar">Cadastrar cliente</button>

    </form>

    <h2>Clientes cadastrados (<?= count($_SESSION["clientes"]) ?>)</h2>

    <?php if (empty($_SESSION["clientes"])): ?>
        <p>Nenhum cliente cadastrado ainda.</p>
    <?php endif; ?>

    <?php foreach ($_SESSION["clientes"] as $i => $c): ?>
        <div class="cliente">
            <div>
                <strong><?= e($c["nome"]) ?></strong><br>
                <small>
                    <?= e($c["email"]) ?> · <?= e($c["telefone"]) ?><br>
                    <?= e($c["rua"]) ?>, <?= e($c["numero"]) ?> - <?= e($c["bairro"]) ?>,
                    <?= e($c["cidade"]) ?>/<?= e($c["uf"]) ?>
                </small>
            </div>
            <form method="POST">
                <input type="hidden" name="indice" value="<?= $i ?>">
                <button type="submit" name="acao" value="remover" class="remover">Remover</button>
            </form>
        </div>
    <?php endforeach; ?>

    <p><a href="index.php">← Voltar para o cadastro de aluno</a></p>

</div>

</body>
</html>
