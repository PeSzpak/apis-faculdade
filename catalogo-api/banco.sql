CREATE DATABASE IF NOT EXISTS catalogo_api
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE catalogo_api;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_api INT NOT NULL,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2),
    categoria VARCHAR(100),
    avaliacao DECIMAL(4,2),
    estoque INT,
    imagem VARCHAR(500),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_id_api (id_api)
);
