<?php
// Funções de consulta compartilhadas entre index.php e clientes.php

// Faz o GET e devolve [status HTTP, objeto JSON ou null].
// ignore_errors faz o PHP ler o corpo mesmo em 404, em vez de devolver false.
function consultarApi($url)
{
    $contexto = stream_context_create([
        "http" => [
            "ignore_errors" => true,
            "timeout" => 10,
            "header" => "User-Agent: cadastro-cep/1.0\r\n",
        ],
    ]);
    $resposta = @file_get_contents($url, false, $contexto);

    if ($resposta === false) {
        return [0, null];
    }

    // PHP 8.4+ tem http_get_last_response_headers(); no 8.2 do XAMPP usa a variável antiga
    $cabecalhos = function_exists("http_get_last_response_headers")
        ? http_get_last_response_headers()
        : $http_response_header;
    preg_match('/\s(\d{3})\s/', $cabecalhos[0] ?? "", $m);
    return [(int) ($m[1] ?? 0), json_decode($resposta)];
}

// Mensagem para respostas que não são 200
function erroApi($status, $naoEncontrado)
{
    if ($status == 0) {
        return "Erro ao consultar a API. Verifique sua conexão.";
    }
    if ($status == 404) {
        return $naoEncontrado;
    }
    if ($status == 429) {
        return "Muitas consultas seguidas. Aguarde alguns segundos e tente de novo.";
    }
    return "Erro ao consultar a API (código $status).";
}

// Devolve [endereço ou null, mensagem de erro]
function buscarCep($cep)
{
    if ($cep == "") {
        return [null, "Informe o CEP."];
    }
    if (strlen($cep) != 8) {
        return [null, "Informe um CEP com 8 números."];
    }

    [$status, $endereco] = consultarApi("https://brasilapi.com.br/api/cep/v1/" . $cep);

    if ($status != 200 || !isset($endereco->cep)) {
        return [null, erroApi($status, "CEP não encontrado.")];
    }
    return [$endereco, ""];
}

// Confere os dois dígitos verificadores do CNPJ
function cnpjValido($cnpj)
{
    if (strlen($cnpj) != 14 || preg_match('/^(\d)\1+$/', $cnpj)) {
        return false;
    }
    $pesos = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    foreach ([12, 13] as $tamanho) {
        $soma = 0;
        for ($i = 0; $i < $tamanho; $i++) {
            $soma += $cnpj[$i] * $pesos[$i + 13 - $tamanho];
        }
        $resto = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;
        if ($cnpj[$tamanho] != $digito) {
            return false;
        }
    }
    return true;
}

// Devolve [empresa ou null, mensagem de erro]
function buscarCnpj($cnpj)
{
    if (!cnpjValido($cnpj)) {
        return [null, "CNPJ inválido."];
    }

    [$status, $empresa] = consultarApi("https://brasilapi.com.br/api/cnpj/v1/" . $cnpj);

    if ($status != 200 || !isset($empresa->cnpj)) {
        return [null, erroApi($status, "CNPJ não encontrado.")];
    }
    return [$empresa, ""];
}

// Atalho para escapar valores no HTML
function e($valor)
{
    return htmlspecialchars((string) ($valor ?? ""));
}
