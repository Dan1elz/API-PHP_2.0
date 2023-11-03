CREATE DATABASE bd_api_teste;

USE bd_api_teste;

CREATE TABLE tb_usuario(
	id_usuario INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome_usuario VARCHAR(20) NOT NULL,
    sobrenome_usuario VARCHAR(20) NOT NULL,
    email_usuario VARCHAR(100) NOT NULL,
	senha_usuario VARCHAR(32) NOT NULL
);

CREATE TABLE tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    token VARCHAR(255) UNIQUE,
    expiration_time TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
