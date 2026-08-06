-- Oi! Aqui é a Manoela, vou fazer alguns comentários para melhor compreenssão do código

CREATE DATABASE IF NOT EXISTS BDhotelfronteira
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
-- Precisei utilizar o 'Character set utf8mb4' porque usei acentos em algumas partes do código, pra evitar
-- que o banco de dados enxergue como caracteres estranhos

USE BDhotelfronteira;

CREATE TABLE usuarios (
	idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(130) NOT NULL UNIQUE,
    senha VARCHAR(130) NOT NULL,
    tipoDeUsuario ENUM('hóspede', 'funcionário', 'dono') NOT NULL DEFAULT 'hóspede'
);

CREATE TABLE catservicos (
	idCat INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

CREATE TABLE servicos (
	idServico INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    idCategoria INT NULL,
    statusDoServico ENUM('disponível','manutenção','fechado') NOT NULL DEFAULT 'disponível',
    motivo VARCHAR(300) NULL,
    atualizadoPor INT NULL,
    atualizadoEm TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- serve para colocar automático a data
    FOREIGN KEY (idCategoria) REFERENCES catservicos(idCat) ON DELETE SET NULL,
    FOREIGN KEY (atualizadoPor) REFERENCES usuarios(idUsuario) ON DELETE SET NULL
);

CREATE TABLE comentarios (
	idComentario INT AUTO_INCREMENT PRIMARY KEY,
    usuarioId INT NOT NULL,
    conteudo TEXT NOT NULL,
    datacomentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- também serve para colocar automático a data
    FOREIGN KEY (usuarioId) REFERENCES usuarios(idUsuario) ON DELETE CASCADE
);