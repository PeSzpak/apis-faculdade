<?php
require "consulta.php";

$mensagem = "";
$endereco = null;
$nome = "";
$nascimento = "";
$email = "";
$cep = "";
$numero = "";
$cnpj = "";
$mensagemCnpj = "";
$empresa = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome"] ?? "";
    $nascimento = $_POST["nascimento"] ?? "";
    $email = $_POST["email"] ?? "";
    $cep = preg_replace('/\D/', '', $_POST["cep"] ?? "");
    $numero = trim($_POST["numero"] ?? "");
    $cnpj = preg_replace('/\D/', '', $_POST["cnpj"] ?? "");

    // 3 - Testar cenários: CEP vazio, incompleto, inexistente e válido
    [$endereco, $mensagem] = buscarCep($cep);

    // 4 - Desafio extra: CNPJ é opcional, só consulta se foi preenchido
    if ($cnpj != "") {
        [$empresa, $mensagemCnpj] = buscarCnpj($cnpj);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #234e70;
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

        .linha > div:first-child {
            flex: 2;
        }

        .linha > div:last-child {
            flex: 1;
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

        .resultado {
            background: #e8f5e9;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }

        /* 2 - Cidade e estado em destaque */
        .destaque {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #234e70;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .destaque .cidade {
            font-size: 26px;
            font-weight: bold;
        }

        .destaque .uf {
            font-size: 22px;
            font-weight: bold;
            background: white;
            color: #234e70;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .situacao {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            background: #c8e6c9;
            color: #1b5e20;
            font-size: 14px;
        }

        .erro {
            color: #b00020;
            margin-top: 15px;
        }

        .opcional {
            color: #777;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Cadastro de Aluno</h1>

    <form method="POST">

        <label>Nome:</label>
        <input
            type="text"
            name="nome"
            value="<?= e($nome) ?>"
            required
        >

        <label>Data de nascimento:</label>
        <input
            type="date"
            name="nascimento"
            value="<?= e($nascimento) ?>"
            required
        >

        <label>E-mail:</label>
        <input
            type="email"
            name="email"
            value="<?= e($email) ?>"
            required
        >

        <!-- CEP sem "required" para o PHP tratar o campo vazio (item 3) -->
        <div class="linha">
            <div>
                <label>CEP:</label>
                <input
                    type="text"
                    name="cep"
                    placeholder="Ex.: 01001000"
                    value="<?= e($cep) ?>"
                    maxlength="9"
                >
            </div>

            <!-- 1 - Campo número da residência -->
            <div>
                <label>Número:</label>
                <input
                    type="text"
                    name="numero"
                    placeholder="Ex.: 123"
                    value="<?= e($numero) ?>"
                    maxlength="10"
                >
            </div>
        </div>

        <!-- 4 - Desafio extra: campo CNPJ -->
        <label>CNPJ: <span class="opcional">(opcional)</span></label>
        <input
            type="text"
            name="cnpj"
            placeholder="Ex.: 33000167000101"
            value="<?= e($cnpj) ?>"
            maxlength="18"
        >

        <button type="submit">Consultar</button>

    </form>

    <?php if ($mensagem != ""): ?>
        <p class="erro">
            <?= e($mensagem) ?>
        </p>
    <?php endif; ?>

    <?php if ($endereco != null): ?>

        <div class="resultado">

            <h2>Endereço encontrado</h2>

            <div class="destaque">
                <span class="cidade"><?= e($endereco->city) ?></span>
                <span class="uf"><?= e($endereco->state) ?></span>
            </div>

            <p><strong>CEP:</strong>
                <?= e($endereco->cep) ?>
            </p>

            <!-- 1 - Número exibido junto ao endereço -->
            <p><strong>Rua:</strong>
                <?= e($endereco->street) ?>, <?= e($numero != "" ? $numero : "s/n") ?>
            </p>

            <p><strong>Bairro:</strong>
                <?= e($endereco->neighborhood) ?>
            </p>

        </div>

    <?php endif; ?>

    <?php if ($mensagemCnpj != ""): ?>
        <p class="erro">
            <?= e($mensagemCnpj) ?>
        </p>
    <?php endif; ?>

    <?php if ($empresa != null): ?>

        <div class="resultado">

            <h2>Dados da empresa</h2>

            <div class="destaque">
                <span class="cidade"><?= e($empresa->nome_fantasia ?: $empresa->razao_social) ?></span>
                <span class="uf"><?= e($empresa->uf) ?></span>
            </div>

            <p><span class="situacao"><?= e($empresa->descricao_situacao_cadastral) ?></span></p>

            <p><strong>Razão social:</strong>
                <?= e($empresa->razao_social) ?>
            </p>

            <p><strong>CNPJ:</strong>
                <?= e($empresa->cnpj) ?>
            </p>

            <p><strong>Atividade:</strong>
                <?= e($empresa->cnae_fiscal_descricao) ?>
            </p>

            <p><strong>Abertura:</strong>
                <?= e(date("d/m/Y", strtotime($empresa->data_inicio_atividade ?? ""))) ?>
            </p>

            <p><strong>Endereço:</strong>
                <?= e(trim(($empresa->descricao_tipo_de_logradouro ?? "") . " " . ($empresa->logradouro ?? ""))) ?>,
                <?= e($empresa->numero) ?> - <?= e($empresa->bairro) ?>,
                <?= e($empresa->municipio) ?>/<?= e($empresa->uf) ?>
            </p>

            <p><strong>Telefone:</strong>
                <?= e($empresa->ddd_telefone_1) ?>
            </p>

        </div>

    <?php endif; ?>

    <p><a href="clientes.php">Ir para o cadastro de clientes →</a></p>

</div>

</body>
</html>
