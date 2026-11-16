<?php
// Conexão com o MySQL do XAMPP (usuário root, sem senha por padrão).
// Para outro ambiente, defina DB_HOST, DB_PORT, DB_NAME, DB_USER e DB_PASS.
$host  = getenv('DB_HOST') ?: 'localhost';
$porta = getenv('DB_PORT') ?: '3306';
$banco = getenv('DB_NAME') ?: 'catalogo_api';
$user  = getenv('DB_USER') ?: 'root';
$senha = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4", $user, $senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Erro ao conectar com o banco de dados: ' . htmlspecialchars($e->getMessage()));
}
