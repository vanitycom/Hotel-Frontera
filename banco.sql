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

-- Inseri alguns dados para testar o funcionamento do nosso banco

INSERT INTO usuarios (nome, telefone, email, senha, tipoDeUsuario) VALUES
('Valmira Valente dos Reis','55984672390','dosreisvalmira@hotel.com', '87654321', 'dono'),
('Júlio Castiel da Silva','55984549087','juliocast@hotel.com', '87654321', 'funcionário'),
('Maria Ferreira Machado','559817393567','mferreiramaria@hotel.com', '12345678', 'hóspede');

INSERT INTO catservicos (nome) VALUES
('Piscina e spa'),
('Academia'),
('Restaurante'),
('Sala de Eventos');

INSERT INTO servicos (nome, descricao, idCategoria, statusDoServico, motivo, atualizadoPor) VALUES
('Piscina climatizada', 'Piscina interior com horário das 08h00 às 22h00', 1, 'disponível', NULL, 2),
('Spa', 'Serviço de Spa com reserva prévia', 1, 'manutenção', 'Reparação no sistema de calefação', 2),
('Academia SuperFit', 'Estrutura com todos os itens necessários para sua rotina de exercícios', 2, 'disponível', NULL, 2),
('Restaurante Coisa Boa', 'Possui todas as três refeições além de uma vista para o jardim', 3, 'disponível', NULL, 2),
('Sala de Eventos Pandora', 'Têm limite para até 80 pessoas', 4, 'fechado', 'O salão está interditado para limpeza', 2);

INSERT INTO comentarios (usuarioId, conteudo) VALUES
(1, 'Sejam bem-vindos ao fórum do Hotel Fronteira! Por favor, mantenham respeito e respeitem as normas do hotel.'),
(3, 'Oii, por acaso alguém sabe me dizer se o a piscina fica aberta ao domingos?');